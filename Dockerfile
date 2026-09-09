# --- Build stage ---
FROM node:22-alpine AS build
WORKDIR /app

COPY package.json package-lock.json ./
RUN npm ci

COPY . .
RUN npm run build

# --- Serve stage ---
FROM httpd:2.4-alpine

RUN sed -i \
    -e 's/^#LoadModule rewrite_module/LoadModule rewrite_module/' \
    /usr/local/apache2/conf/httpd.conf \
    && echo 'ServerName baischer-fleischerei-catering.com' >> /usr/local/apache2/conf/httpd.conf \
    && echo 'Include conf/extra/vuejs.conf' >> /usr/local/apache2/conf/httpd.conf

COPY apache.conf /usr/local/apache2/conf/extra/vuejs.conf
COPY --from=build /app/dist /usr/local/apache2/htdocs

EXPOSE 80
