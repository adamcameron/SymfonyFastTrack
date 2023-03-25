FROM node:19-bullseye

RUN ["apt-get", "update"]
RUN ["apt-get", "install", "-y", "vim"]

COPY home/.bashrc /root/.bashrc
COPY home/.bash_history /root/.bash_history
COPY home/.vimrc /root/.vimrc

WORKDIR  /usr/share/nodejs/
