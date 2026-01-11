FROM nginx:stable-alpine

# Remove default conf
RUN rm /etc/nginx/conf.d/default.conf

# Copy custom nginx config
COPY dockerfiles/nginx.conf /etc/nginx/conf.d/default.conf

WORKDIR /var/www

EXPOSE 8000
