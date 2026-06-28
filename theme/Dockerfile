FROM ubuntu:24.04

RUN apt-get update && apt-get install -y --no-install-recommends \
    make \
    dpkg-dev \
    rpm \
    && rm -rf /var/lib/apt/lists/*

WORKDIR /build
COPY . .

CMD make deb && make rpm && make tar.gz && \
    mkdir -p /out && \
    cp clarity-icons_*.deb clarity-icons-*.rpm clarity-icons-*.tar.gz /out/
