.PHONY: build

build:
	docker build -f build/Dockerfile -t zcorrecteurs/monolith-www .
