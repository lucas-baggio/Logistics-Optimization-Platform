Este diretório contém arquivos de apoio para o container root (gateway).

Instruções rápidas para executar os containers em uma rede compartilhada (exemplo):

1. Crie a rede Docker compartilhada:

```bash
docker network create lop-network
```

2. Construa e rode o container root (gateway) a partir da raiz do repositório:

```bash
# build do gateway (assume que está na raiz do repositório)
docker build -f Dockerfile.root -t lop/gateway .

# roda o gateway conectado à rede compartilhada
docker run -d --name gateway --network lop-network lop/gateway
```

3. Construa e rode o backend (assumindo build context = pasta `backend`):

```bash
docker build -t lop/backend ./backend
docker run -d --name backend --network lop-network -e GATEWAY_HOST=gateway lop/backend
```

4. Construa e rode o frontend (assumindo build context = pasta `frontend`):

```bash
docker build -t lop/frontend ./frontend
# publica porta 4200 local -> 80 do container (ajuste conforme necessário)
docker run -d --name frontend --network lop-network -e GATEWAY_HOST=gateway -p 4200:80 lop/frontend
```

Observações:
- Os `Dockerfile` de `backend` e `frontend` assumem que você vai executar o `docker build` com o contexto correto (ou seja, a pasta `backend` e `frontend`, respectivamente).
- O container root/gateway expõe o hostname `gateway` dentro da rede `lop-network`. Ambos os serviços podem usar a variável de ambiente `GATEWAY_HOST=gateway` para localizar o serviço root.
- Você mencionou que criará um script na raiz que executa primeiro o container root — o README acima mostra os comandos que o script pode executar em sequência.

5. Adicionar e inicializar MySQL (opção via Compose — recomendado)

```bash
# o serviço 'db' será iniciado com as variáveis definidas em docker-compose.yml
docker compose up -d db
# o MySQL criará o database 'lop_db' e o usuário 'lop_user' com senha 'lop_pass' usando docker/mysql/init.sql
```

Observação: as credenciais padrão configuradas no `docker-compose.yml` são:
- root: `root_pass`
- database: `lop_db`
- user: `lop_user`
- password: `lop_pass`
