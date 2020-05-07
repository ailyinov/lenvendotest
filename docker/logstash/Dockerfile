FROM docker.elastic.co/logstash/logstash:7.6.2

USER root

RUN yum install -y mysql-connector-java

RUN rm -f /usr/share/logstash/pipeline/logstash.conf
ADD pipeline/ /usr/share/logstash/pipeline/