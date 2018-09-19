<?php
/**
 * SimplePHPServer class manage our options to wrap our PHP Builtin server
 * 
 * PHP builtin server never checks if the provided port is already used
 * This script is intended to automatically choose another port if the provided port is already taken.
 * Remember: This class or the builtin PHP Server are designed ONLY to aid application development, testing or demonstration.
 * Don't use it in a production environment
 * 
 * @package SimplePHPServer
 * @author Benfarhat Elyes <benfarhat.elyes@gmail.com>
 * @version 1.0.0
 * @see http://php.net/manual/en/features.commandline.webserver.php
 */

namespace SimplePHPServer;

class SimplePHPServer
{

    /**
     * @var string $host    adresse wich will listen for connection
     */
    private $host;

    /**
     * @var int $port   port for connection
     */
    private $port;

    /**
     * @var string $directory   should refer to the "front" point
     */
    private $directory;

    /**
     * @var string $version     version of the script
     */
    private $version = '1.0.0';

    /**
     * @var integer $retry      if port is not available, we retry with the next $retry ports
     */
    private $retry = 100;

    /**
     * Array of arguments passed to script
     *
     * @param array $argv
     */
    public function __construct($argv)
    {
        $merged = array_merge([
            'host' => '127.0.0.1',
            'port' => 8000,
            'directory' => __DIR__
            ], $this->parseArgV($argv));
        $this->host = $merged['host'];
        $this->port = $merged['port'];
        $this->directory = escapeshellarg($merged['directory']);
        
        if($this->checkPort()){
            $this->run();
        } else {
            $this->printf(sprintf('Port from %s to %s are not available.', $this->port, $this->port + $this->retry));
        }
    }

    /**
     * Parse scripts's arguments
     *
     * @param array $args
     * @return array
     */
    private function parseArgv($args): array
    {
        $params = [];
        // We don't need script name (our first element)
        array_shift($args);

        foreach($args as $arg){

            // if it start with '-' or '--'
            if(strpos($arg, '-') == 0){
                // here we can use ":" separator or "=" (between param name and value)
                if((strpos($arg,':') !== false) || (strpos($arg,'=') !== false)){
                    $val = explode('=', $arg);
                    if(count($val) == 2){
                        $params[ltrim($val[0], '-')] = $val[1];
                    } else {
                    $val = explode(':', $arg);           
                    $params[ltrim($val[0], '-')] = $val[1]; 
                    }
                } else {
                    if(strpos($arg,'-') === 0) {
                        // We need next function to get values which are separate from their params name with a space
                        $next = next($args);   
                        prev($args);
                        if(strpos($next,'-') === false){
                            $params[ltrim($arg, '-')] = $next;
                        } else {
                            $params[ltrim($arg, '-')] = null;
                        }
                    }
                }
            }
            next($args);
        }

        // We finally try to have the save name for each option for example 'p' will be 'port'
        $sameKey = [
            'h' => 'host',
            'p' => 'port',
            'd' => 'directory'
        ];

        $json = json_encode($params);
        foreach($sameKey as $k => $v){
            $json = str_replace('"'.$k.'":', '"'.$v.'":', $json);
        }
        return json_decode($json, true); 
    }

    /**
     * checkPort function
     * 
     * It checks if the port is used, if not we increment the port ($this->retry times)
     *
     * @return bool
     */
    private function checkPort(){
        for($i=0; $i<$this->retry; $i++){
            $connection = @fsockopen($this->host, $this->port);
            if(is_resource($connection)){
                $this->printf(sprintf('Port %s is not available ...', $this->port));
                $this->port++;
                fclose($connection);
            } else {
                return true;
            }
        }
        return false;

    }
    /**
     * run function
     * 
     * Display some usefull information and launch the PHP builtin server
     *
     * @return void
     */
    private function run(){
        $command = sprintf(
            'php -S %s:%d -t %s',
            $this->host,
            $this->port,
            $this->directory
        );
        $url = $this->host . ':' . $this->port;
        $intro = sprintf('SimplePHPServer %s started at %s', $this->version, date("D M j G:i:s T Y"));
        $this->printf(str_repeat("*", strlen($intro)),1,1);
        $this->printf($intro,1);
        $this->printf(str_repeat("*", strlen($intro)),2);
        $this->printf(sprintf('Listening on http://%s/', $url));
        $this->printf(sprintf('Document root is %s', $this->directory));
        
        $this->printf(str_repeat("*", strlen($intro)),1,1);
        $this->printf('Press Ctrl-C to quit.',2);
        system(escapeshellcmd($command), $result);
    }

    /**
     * printf function
     * 
     * Display a message and add some newline (before and after)
     *
     * @param string $message Message to display
     * @param integer $after Number of newline before the message (default to 0)
     * @param integer $before Number of newline after the message (default to 1)
     * @return void
     */
    private function printf(string $message, int $after = 1, int $before = 0){
        
        print str_repeat(PHP_EOL, $before);
        print($message);
        print str_repeat(PHP_EOL, $after);
    }
}
new SimplePHPServer($argv);