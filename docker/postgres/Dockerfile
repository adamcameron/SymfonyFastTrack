FROM postgres:15-bullseye

RUN ["apt-get", "update"]
RUN ["apt-get", "install", "-y", "vim", "iputils-ping"]

COPY postgres/home/.bash_history /root/.bash_history
COPY shared/home/.bashrc /root/.bashrc
COPY shared/home/.vimrc /root/.vimrc

COPY postgres/docker-entrypoint-initdb.d/ /docker-entrypoint-initdb.d/

EXPOSE 5432
