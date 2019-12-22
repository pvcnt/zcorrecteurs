.PHONY: all

all: webserver sphinx

webserver:
	docker build -f build/webserver/Dockerfile -t zcorrecteurs-webserver .

sphinx:
	docker build -f build/sphinx/Dockerfile -t zcorrecteurs-sphinx .
