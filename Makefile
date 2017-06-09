PROJ = fruits

EXC = --exclude '.git*' --exclude '*.swp' --exclude Makefile

OPTS = -avz ${EXC} --delete-excluded

deploy-testing:
	rsync ${OPTS} . root@127.0.0.1:/home/deploy/public/${PROJ}

deploy-production:
	rsync ${OPTS} -e "ssh -p 1022" . op1@127.0.0.1:/opt/www/${PROJ}
