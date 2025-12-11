# Build stage
FROM node:18 AS build

WORKDIR /app

# Copia manifests e instala dependências (assume build context = frontend)
COPY package.json package-lock.json ./
RUN npm ci --silent

# Copia o resto e builda a aplicação Angular
COPY . .
RUN npm run build -- --configuration production

# Production stage (serve com nginx)
FROM nginx:stable-alpine

# Copia build para nginx
COPY --from=build /app/dist /usr/share/nginx/html

ENV GATEWAY_HOST=gateway

EXPOSE 80

CMD ["nginx", "-g", "daemon off;"]
