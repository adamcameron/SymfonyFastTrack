FROM nginx

COPY nginx/home/.bash_history /root/.bash_history
COPY shared/home/.bashrc /root/.bashrc
COPY shared/home/.vimrc /root/.vimrc

COPY nginx/nginx.conf /etc/nginx/nginx.conf
COPY nginx/sites/ /etc/nginx/sites-available/
COPY nginx/conf.d/ /etc/nginx/conf.d/

WORKDIR /usr/share/nginx/

CMD ["nginx"]
EXPOSE 80
