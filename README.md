# suzie-queue

Hey guys!

I've got the queue stored in /srv/queue
We should each have a checkout of the repo stored as /srv/queue/$USER

To set this up,
```
sudo su -l
cd /srv/queue
git clone https://github.com/HeroOfCanton/suzie-queue.git CADE-USER-NAME
chown -R CADE-USER-NAME CADE-USER-NAME
```
