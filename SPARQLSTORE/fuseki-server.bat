@echo off
@REM modify this to name the server jar
java -Xmx1200M -jar fuseki-server.jar -update -port=3030 -loc=/data/ds/ /ds
