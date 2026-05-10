FROM node:20-alpine AS builder

WORKDIR /app

COPY docs-site/package.json docs-site/package-lock.json* ./
RUN npm install

COPY docs-site/ .
RUN npm run build

FROM nginx:alpine

COPY --from=builder /app/.vitepress/dist /usr/share/nginx/html
COPY docs-site/nginx.conf /etc/nginx/conf.d/default.conf

EXPOSE 80

CMD ["nginx", "-g", "daemon off;"]
