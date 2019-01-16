<?php

    class Station {
        private $code;
        private $punch_id;
        private $timestamps;

        public function __construct($code){
            $this->code = $code;
            $this->punch_id = 0;
        }

        private function getPunchId(){
            return $this->punch_id++;
        }

        public function addTimestamp($si_unit, $timestamp, $send_time){
            $this->timestamps[] = array("pid" => $this->getPunchId(), "code" => $this->code, "si_unit" => $si_unit, "timestamp" => $timestamp, "send_time" => $send_time);
        }

        public function getTimestamps(){
            return $this->timestamps;
        }
    }