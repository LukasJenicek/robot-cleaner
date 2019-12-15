# Robot Cleaner

Assignment task: Program robot which will execute all of the commands if the situation permits it. Under some circumstances commands cannot be executed: Out of energy, Robot is stuck ... 

## Execute the program  
1. Install libraries by executing `composer install`
2. Then execute program by `php cleaning_robot.php source-file destination-file`

## Execute the program via docker
1. First build a docker image with this command `docker build . -t "robot-cleaner:1.0.0"`
2. Then run the docker image `docker run -it -v $(pwd)/resources:/app/resources robot-cleaner:1.0.0 resources/test2.json resources/test2_output.json`
