#!/usr/bin/env bash
set -euo pipefail

# start.sh - conveniência para iniciar o ambiente (prod ou dev)
# Uso:
#   ./start.sh        -> sobe ambiente de produção (gateway, backend, frontend)
#   ./start.sh --dev  -> sobe ambiente de desenvolvimento (gateway, backend-dev, frontend-dev)

NETWORK=lop-network
COMPOSE_CMD=""

# Skip starting DB container when using an external DB (set SKIP_DB=true)
SKIP_DB=${SKIP_DB:-true}

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
  if [ "$SKIP_DB" != "true" ]; then
    check_and_free_port 3306
  fi

  # remove existing containers with the same names to avoid name conflicts
  RM_CONTAINERS=(gateway backend-dev frontend-dev)
  if [ "$SKIP_DB" != "true" ]; then
    RM_CONTAINERS+=(db)
  fi
  for c in "${RM_CONTAINERS[@]}"; do
    if docker ps -a --format '{{.Names}}' | grep -xq "$c"; then
      echo "[start.sh] Removendo container existente: $c"
      docker rm -f "$c" || true
    fi
  done

  # build and up dev services (inclui db a menos que SKIP_DB=true)
  SERVICES_TO_BUILD=(gateway backend-dev frontend-dev)
  SERVICES_TO_UP=(gateway backend-dev frontend-dev)
  if [ "$SKIP_DB" != "true" ]; then
    SERVICES_TO_BUILD+=(db)
    SERVICES_TO_UP+=(db)
  fi
  $COMPOSE_CMD build "${SERVICES_TO_BUILD[@]}"
  $COMPOSE_CMD up -d --remove-orphans "${SERVICES_TO_UP[@]}"
else
  echo "[start.sh] Modo produção: subindo gateway + backend + frontend"
  # remove existing containers with the same names to avoid name conflicts
  # ensure host ports are free (4200 for frontend, 9000 for backend, 8080 for gateway)
  check_and_free_port 4200
  check_and_free_port 9000
  check_and_free_port 8080
  if [ "$SKIP_DB" != "true" ]; then
    check_and_free_port 3306
  fi
  RM_CONTAINERS=(gateway backend frontend)
  if [ "$SKIP_DB" != "true" ]; then
    RM_CONTAINERS+=(db)
  fi
  for c in "${RM_CONTAINERS[@]}"; do
    if docker ps -a --format '{{.Names}}' | grep -xq "$c"; then
      echo "[start.sh] Removendo container existente: $c"
      docker rm -f "$c" || true
    fi
  done
  SERVICES_TO_BUILD=(gateway backend frontend)
  SERVICES_TO_UP=(gateway backend frontend)
  if [ "$SKIP_DB" != "true" ]; then
    SERVICES_TO_BUILD+=(db)
    SERVICES_TO_UP+=(db)
  fi
  $COMPOSE_CMD build "${SERVICES_TO_BUILD[@]}"
  $COMPOSE_CMD up -d --remove-orphans "${SERVICES_TO_UP[@]}"
fi

print_link() {
  # Use ANSI OSC 8 hyperlink if terminal supports, otherwise fall back to plain URL
  local label="$1" url="$2"
  # OSC-8 format: \x1b]8;;<url>\x1b\<label>\x1b]8;;\x1b\\
  if [ -n "$url" ]; then
    # Print the prefix in a way that avoids printf parsing any leading '-'
    printf '%s' "- $label: "
    # Print clickable label using OSC 8 (ESC ] 8 ;; URI BEL label ESC ] 8 ;; BEL) and also the URL for plain terminals
    # Use \033 for ESC and \007 (BEL) as string terminator to avoid escaping complexity
    printf '\033]8;;%s\007%s\033]8;;\007\n' "$url" "$url"
  else
    printf '%s\n' "- $label: (no host mapping, see container)"
  fi
}

echo "[start.sh] Serviços iniciados (modo: $( [ "$DEV" = true ] && echo dev || echo prod ) )."

# Decide URLs (host-accessible) depending on dev/prod mode
GATEWAY_URL="http://localhost:8080"
FRONTEND_URL="http://localhost:4200"
BACKEND_DEV_URL="http://localhost:9000"

echo "Abaixo estão os links para acessar os serviços (em ordem: frontend, gateway, API status, backend, database):"

# 1) Frontend
print_link "Frontend" "$FRONTEND_URL"

# 2) Gateway (entry point / reverse proxy)
print_link "Gateway (UI/API entry)" "$GATEWAY_URL"

# 3) API Status endpoint
if [ "$DEV" = true ]; then
  print_link "API Status" "${BACKEND_DEV_URL}/api/status"
else
  print_link "API Status" "${GATEWAY_URL}/api/status"
fi

# 4) Backend (dev only has host mapping)
if [ "$DEV" = true ]; then
  print_link "Backend (dev)" "$BACKEND_DEV_URL"
else
  # Production backend container does not expose a host port by default – show internal container URL
  printf '%s\n' "- Backend (prod): http://backend:9000 (container-only; access via gateway)"
fi

# 5) Database information (internal container)
if [ "$SKIP_DB" != "true" ]; then
  printf '%s\n' "- Database: db:3306 (MySQL, container-only)."
  printf '%s\n' "  Se precisar de administração via web, adicione phpMyAdmin/ADMINER no docker-compose e exponha a porta 8081 por exemplo."
else
  # Determine DB host from environment or backend/.env; default to host.docker.internal for Docker Desktop
  DB_HOST_DISPLAY=${DB_HOST:-$(grep -E '^DB_HOST=' backend/.env 2>/dev/null | cut -d'=' -f2 || true)}
  if [ -z "$DB_HOST_DISPLAY" ]; then
    DB_HOST_DISPLAY=host.docker.internal
  fi
  printf '%s\n' "- Database: external (using $DB_HOST_DISPLAY)."
  printf '%s\n' "  Certifique-se de que $DB_HOST_DISPLAY esteja acessível e as credenciais em backend/.env estejam corretas."
fi

