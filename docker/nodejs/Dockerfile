FROM node:19-bullseye

RUN ["apt-get", "update"]
RUN ["apt-get", "install", "-y", "vim"]

COPY nodejs/home/.bash_history /root/.bash_history
COPY shared/home/.bashrc /root/.bashrc
COPY shared/home/.vimrc /root/.vimrc

WORKDIR  /usr/share/nodejs/
