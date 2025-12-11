#!/usr/bin/env bash
set -euo pipefail

# start.sh - conveniência para iniciar o ambiente (prod ou dev)
# Uso:
#   ./start.sh        -> sobe ambiente de produção (gateway, backend, frontend)
#   ./start.sh --dev  -> sobe ambiente de desenvolvimento (gateway, backend-dev, frontend-dev)

NETWORK=lop-network
COMPOSE_CMD=""

if command -v docker >/dev/null 2>&1; then
  if docker info >/dev/null 2>&1; then
    : # daemon acessível
  else
    echo "[start.sh] Docker daemon inacessível — tentando iniciar ~/start-dockerd.sh"
    if [ -x "$HOME/start-dockerd.sh" ]; then
      "$HOME/start-dockerd.sh" || true
      sleep 1
    fi
    if ! docker info >/dev/null 2>&1; then
      echo "[start.sh] Não foi possível conectar ao daemon Docker. Saia e execute start-dockerd.sh ou ative Docker Desktop/WSL kernel atualizado." >&2
      exit 1
    fi
  fi
else
  echo "[start.sh] 'docker' não encontrado no PATH." >&2
  exit 1
fi

# detect docker compose command
if docker compose version >/dev/null 2>&1; then
  COMPOSE_CMD="docker compose"
elif command -v docker-compose >/dev/null 2>&1; then
  COMPOSE_CMD="docker-compose"
else
  echo "[start.sh] nem 'docker compose' nem 'docker-compose' disponíveis." >&2
  exit 1
fi

echo "[start.sh] Verificando rede Docker '$NETWORK'..."
if ! docker network inspect "$NETWORK" >/dev/null 2>&1; then
  docker network create "$NETWORK"
  echo "[start.sh] Rede '$NETWORK' criada."
else
  echo "[start.sh] Rede '$NETWORK' já existe."
fi

# parse flags
DEV=false
if [ "${1:-}" = "--dev" ] || [ "${1:-}" = "-d" ]; then
  DEV=true
fi

check_and_free_port() {
  local port=$1
  # check if any process is listening on the port
  if ss -ltnp 2>/dev/null | grep -w ":$port" >/dev/null 2>&1; then
    # try to find a docker container bound to that host port
    local entry
    entry=$(docker ps --format '{{.Names}}\t{{.Ports}}' | grep -E ":$port(->|[^0-9]|$)" || true)
    if [ -n "$entry" ]; then
      local cname
      cname=$(echo "$entry" | awk -F'\t' '{print $1}')
      echo "[start.sh] Porta $port está em uso pelo container $cname — removendo..."
      docker rm -f "$cname" || true
      return 0
    fi

    # not a docker container -> host process. show info and abort.
    echo "[start.sh] Porta $port está em uso por um processo no host e não por um container." >&2
    echo "Use 'ss -ltnp | grep :$port' para ver o processo e libere a porta, ou execute start.sh após liberar." >&2
    ss -ltnp | grep -w ":$port" || true
    exit 1
  fi
}

if [ "$DEV" = true ]; then
  echo "[start.sh] Modo desenvolvimento: subindo gateway + backend-dev + frontend-dev"
  # ensure host ports are free (4200 for frontend-dev, 9000 for backend-dev, 8080 for gateway)
  check_and_free_port 4200
  check_and_free_port 9000
  check_and_free_port 8080

  # remove existing containers with the same names to avoid name conflicts
  for c in gateway backend-dev frontend-dev; do
    if docker ps -a --format '{{.Names}}' | grep -xq "$c"; then
      echo "[start.sh] Removendo container existente: $c"
      docker rm -f "$c" || true
    fi
  done

  # build and up dev services (inclui db)
  $COMPOSE_CMD build gateway db backend-dev frontend-dev
  $COMPOSE_CMD up -d --remove-orphans gateway db backend-dev frontend-dev
else
  echo "[start.sh] Modo produção: subindo gateway + backend + frontend"
  # remove existing containers with the same names to avoid name conflicts
  # ensure host ports are free (4200 for frontend, 9000 for backend, 8080 for gateway)
  check_and_free_port 4200
  check_and_free_port 9000
  check_and_free_port 8080

  for c in gateway backend frontend db; do
    if docker ps -a --format '{{.Names}}' | grep -xq "$c"; then
      echo "[start.sh] Removendo container existente: $c"
      docker rm -f "$c" || true
    fi
  done
  $COMPOSE_CMD build gateway db backend frontend
  $COMPOSE_CMD up -d --remove-orphans gateway db backend frontend
fi

echo "[start.sh] Serviços iniciados (modo: $( [ "$DEV" = true ] && echo dev || echo prod ) )."
echo "- gateway: container 'gateway' na rede $NETWORK (porta 8080 mapeada)"
echo "- backend: container 'backend' (ou 'backend-dev') na rede $NETWORK (porta 9000 interna)"
echo "- frontend: container 'frontend' (ou 'frontend-dev') na rede $NETWORK (porta 4200 mapeada)"
