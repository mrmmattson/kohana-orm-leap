----
-- Copyright 2012 Spadefoot
--
-- Licensed under the Apache License, Version 2.0 (the "License");
-- you may not use this file except in compliance with the License.
-- You may obtain a copy of the License at
--
--      http://www.apache.org/licenses/LICENSE-2.0
--
-- Unless required by applicable law or agreed to in writing, software
-- distributed under the License is distributed on an "AS IS" BASIS,
-- WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
-- See the License for the specific language governing permissions and
-- limitations under the License.
----

----
-- Table structure for the "mptt_example" table
----

CREATE TABLE `mptt_example` (
	"id" NUMBER(11) NOT NULL,
	`name` VARCHAR(35) NOT NULL DEFAULT '',
	"lft" NUMBER(11) NOT NULL,
	"rgt" NUMBER(11) NOT NULL,
	"lvl" NUMBER(11) NOT NULL,
	"scope" NUMBER(11) NOT NULL,
	CONSTRAINT "mptt_example_id_pkey" PRIMARY KEY ("id")
);

----
-- Auto-increment the "mptt_example" table (see, http://earlruby.org/2009/01/creating-auto-increment-columns-in-oracle/)
----

CREATE SEQUENCE "mptt_example_id_seq" START WITH 1 INCREMENT BY 1;

CREATE TRIGGER "mptt_example_id_trig" BEFORE INSERT ON "mptt_example" FOR EACH ROW
DECLARE
    max_id NUMBER;
    cur_seq NUMBER;
BEGIN
    IF :new.id IS NULL THEN
        -- No ID passed, get one from the sequence
        SELECT "mptt_example_id_seq".nextval INTO :new.id FROM dual;
    ELSE
        -- ID was set via insert, so update the sequence
        SELECT greatest(nvl(max(id),0), :new.id) INTO max_id FROM "mptt_example";
        SELECT "mptt_example_id_seq".nextval INTO cur_seq FROM dual;
        WHILE cur_seq < max_id
        LOOP
            SELECT "mptt_example_id_seq".nextval INTO cur_seq FROM dual;
        END LOOP;
    END IF;
END;
