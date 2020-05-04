ARG IMAGE
FROM ${IMAGE}

# packages
ARG PACKAGES
RUN if [ "${PACKAGES}" ]; then apk update && apk add -f ${PACKAGES}; fi

# php modules
ARG PHPMODS
RUN if [ "${PHPMODS}" ]; then docker-php-ext-install ${PHPMODS}; fi

# composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

VOLUME /workdir
WORKDIR /workdir

COPY "./docker-entrypoint.tests.sh" "/entrypoint.sh"
ENTRYPOINT ["/bin/sh", "/entrypoint.sh"]
