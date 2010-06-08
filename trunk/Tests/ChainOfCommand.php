<?php
abstract class Logger {
	const ERR = 3;
	const NOTICE = 5;
	const DEBUG = 7;

	protected $mask;
	protected $next; // The next element in the chain of responsibility

	public function setNext(Logger $l) {
		$this->next = $l;
		return $this;
	}

	public function message($msg, $priority) {
	
		if ($priority <= $this->mask) {
			$this->writeMessage( $msg );
		}

		if (false == is_null($this->next)) {
			$this->next->message($msg, $priority);
		}
	}
	
	abstract public function writeMessage($msg );
}

class DebugLogger extends Logger {
	public function __construct($mask) {
		$this->mask = $mask;
		return $this;
	}

	public function writeMessage($msg ) {
			echo "Writing to debug output: {$msg}\n";
	}
}

class EmailLogger extends Logger {
	public function __construct($mask) {
		$this->mask = $mask;
		return $this;		
	}

	public function writeMessage($msg ) {
			echo "Sending via email: {$msg}\n";
	}
}

class StderrLogger extends Logger {
	public function __construct($mask) {
		$this->mask = $mask;
		return $this;		
	}

	public function writeMessage($msg ) {
			echo "Writing to stderr: {$msg}\n";
	}
}

class ChainOfResponsibilityExample {
	public function __construct() {
		// build the chain of responsibility
		$l = new DebugLogger(Logger::DEBUG);
		$e = new EmailLogger(Logger::NOTICE);
		$s = new StderrLogger(Logger::ERR);
		$l->setNext( $e->setNext( $s ) );  

		$l->message("Entering function y.",		Logger::DEBUG);		// handled by DebugLogger
		$l->message("Step1 completed.",			Logger::NOTICE);	// handled by DebugLogger and EmailLogger
		$l->message("An error has occurred.",	Logger::ERR);		// handled by all three Loggers
	}
}

new ChainOfResponsibilityExample();
