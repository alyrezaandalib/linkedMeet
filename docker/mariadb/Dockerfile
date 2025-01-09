FROM mariadb:lts

ARG UID
ARG GID

ENV UID=${UID}
ENV GID=${GID}

RUN groupadd -g ${GID} linkedmeet
RUN adduser --uid ${UID} --gid ${GID} --system --shell /bin/sh linkedmeet

RUN chown -R linkedmeet:linkedmeet /var/lib/mysql /var/run/mysqld

COPY docker/mariadb/scripts/init.sql /docker-entrypoint-initdb.d/init.sql

USER linkedmeet

EXPOSE 3306
