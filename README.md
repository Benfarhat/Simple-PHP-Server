# Simple-PHP-Server
This script is a wrapper for the builtin PHP server, it is intended to automatically choose another port if the provided port is already taken.

If you choose to test your php development code on port 8080, and the script detect that this port is already used, it try to choose another resource (a simple increment) 100 times


for the moment, this class work with 3 options, all the combinaison of using option prefix "-" or "--" and putting beteween option name ":" or"=" or space, are possible.

## Host

  -h 127.0.0.1
  --h:127.0.0.1
  -host=127.0.0.1

## Port (default 8000)

  -p 8000
  --port:8000
  --p:8000

## Directory (default __DIR__) to fix

WIP
