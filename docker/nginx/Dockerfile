FROM nginx

COPY home/.bashrc /root/.bashrc
COPY home/.bash_history /root/.bash_history
COPY home/.vimrc /root/.vimrc

WORKDIR /usr/share/nginx/
COPY ./nginx.conf /etc/nginx/nginx.conf
COPY ./sites/ /etc/nginx/sites-available/
COPY ./conf.d/ /etc/nginx/conf.d/
CMD ["nginx"]
EXPOSE 80
