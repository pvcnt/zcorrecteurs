FROM ubuntu:xenial

# software-properties-common est requis pour add-apt-repository
# gettext-base est requis pour envsubst
RUN apt-get update \
  && apt-get install -y --no-install-recommends \
    software-properties-common \
    gettext-base

RUN add-apt-repository ppa:builds/sphinxsearch-rel22 \
  && apt-get update \
  && apt-get install -y --no-install-recommends sphinxsearch \
  && rm -rf /var/lib/apt/lists/*

VOLUME /var/lib/sphinx

COPY etc/sphinx/sphinx.conf.template etc/sphinx/stopwords.txt /etc/sphinxsearch/
COPY build/sphinx/reindex.sh /usr/local/bin/reindex
COPY build/sphinx/entrypoint.sh /

RUN chmod +x /entrypoint.sh && chmod +x /usr/local/bin/reindex

ENTRYPOINT ["/entrypoint.sh"]
CMD ["serve"]