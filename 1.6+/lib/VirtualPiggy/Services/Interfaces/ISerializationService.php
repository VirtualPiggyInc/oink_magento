<?php
/**
 * @package VirtualPiggy.Services.Interfaces
 */
    interface ISerializationService {
		/*
		 * Only used for testing.
		 * */
        public function SerializeObject($object,$schemaPath);
        public function DeserializeObject($serialized);
    }
?>
