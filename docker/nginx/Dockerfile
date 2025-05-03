FROM nginx:1.21-alpine

WORKDIR /var/www/html

COPY nginx.conf /etc/nginx/

RUN rm /etc/nginx/conf.d/default.conf

EXPOSE 80

CMD ["nginx", "-g", "daemon off;"]
