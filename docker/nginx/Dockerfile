FROM nginx:stable-alpine

LABEL authors="Moein Bakhtnama"

ARG UID
ARG GID

ENV UID=${UID}
ENV GID=${GID}

RUN delgroup dialout

RUN addgroup -g ${GID} --system linkedmeet
RUN adduser -G linkedmeet --system -D -s /bin/sh -u ${UID} linkedmeet
RUN sed -i "s/user  nginx/user linkedmeet/g" /etc/nginx/nginx.conf

RUN mkdir -p /var/www/html
