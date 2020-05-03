FROM debian:stretch
RUN apt-get update && apt-get install -y nginx vim nano
RUN rm /etc/nginx/sites-enabled/default
COPY nginx.conf /etc/nginx/nginx.conf
ADD sites-enabled /etc/nginx/sites-enabled
WORKDIR /var/www/project
EXPOSE 80
EXPOSE 443
CMD ["nginx"]