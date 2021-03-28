--
-- PostgreSQL database dump
--

SET statement_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SET check_function_bodies = false;
SET client_min_messages = warning;

SET search_path = public, pg_catalog;

--
-- Name: gender; Type: TYPE; Schema: public; Owner: xsmerda
--

CREATE TYPE gender AS ENUM (
    'male',
    'female'
);


ALTER TYPE public.gender OWNER TO xsmerda;

SET default_tablespace = '';

SET default_with_oids = false;

--
-- Name: in_room; Type: TABLE; Schema: public; Owner: xsmerda; Tablespace: 
--

CREATE TABLE in_room (
    id_users integer NOT NULL,
    id_rooms integer NOT NULL,
    last_message timestamp without time zone NOT NULL,
    entered timestamp without time zone NOT NULL
);


ALTER TABLE public.in_room OWNER TO xsmerda;

--
-- Name: messages_id_messages_seq; Type: SEQUENCE; Schema: public; Owner: xsmerda
--

CREATE SEQUENCE messages_id_messages_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.messages_id_messages_seq OWNER TO xsmerda;

--
-- Name: messages_id_messages_seq; Type: SEQUENCE SET; Schema: public; Owner: xsmerda
--

SELECT pg_catalog.setval('messages_id_messages_seq', 21, true);


--
-- Name: messages; Type: TABLE; Schema: public; Owner: xsmerda; Tablespace: 
--

CREATE TABLE messages (
    id_messages integer DEFAULT nextval('messages_id_messages_seq'::regclass) NOT NULL,
    id_rooms integer NOT NULL,
    id_users_from integer NOT NULL,
    id_users_to integer,
    created timestamp without time zone NOT NULL,
    message character varying(255) NOT NULL
);


ALTER TABLE public.messages OWNER TO xsmerda;

--
-- Name: room_kick; Type: TABLE; Schema: public; Owner: xsmerda; Tablespace: 
--

CREATE TABLE room_kick (
    id_users integer NOT NULL,
    id_rooms integer NOT NULL,
    created timestamp without time zone NOT NULL
);


ALTER TABLE public.room_kick OWNER TO xsmerda;

--
-- Name: rooms_id_rooms_seq; Type: SEQUENCE; Schema: public; Owner: xsmerda
--

CREATE SEQUENCE rooms_id_rooms_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.rooms_id_rooms_seq OWNER TO xsmerda;

--
-- Name: rooms_id_rooms_seq; Type: SEQUENCE SET; Schema: public; Owner: xsmerda
--

SELECT pg_catalog.setval('rooms_id_rooms_seq', 18, true);


--
-- Name: rooms; Type: TABLE; Schema: public; Owner: xsmerda; Tablespace: 
--

CREATE TABLE rooms (
    id_rooms integer DEFAULT nextval('rooms_id_rooms_seq'::regclass) NOT NULL,
    created timestamp without time zone NOT NULL,
    title character varying(100) NOT NULL,
    id_users_owner integer NOT NULL,
    lock boolean DEFAULT false NOT NULL
);


ALTER TABLE public.rooms OWNER TO xsmerda;

--
-- Name: users_id_users_seq; Type: SEQUENCE; Schema: public; Owner: xsmerda
--

CREATE SEQUENCE users_id_users_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.users_id_users_seq OWNER TO xsmerda;

--
-- Name: users_id_users_seq; Type: SEQUENCE SET; Schema: public; Owner: xsmerda
--

SELECT pg_catalog.setval('users_id_users_seq', 8, true);


--
-- Name: users; Type: TABLE; Schema: public; Owner: xsmerda; Tablespace: 
--

CREATE TABLE users (
    id_users integer DEFAULT nextval('users_id_users_seq'::regclass) NOT NULL,
    login character varying(100) NOT NULL,
    email character varying(255) NOT NULL,
    password character varying(255) NOT NULL,
    name character varying(100) NOT NULL,
    surname character varying(100) NOT NULL,
    gender gender NOT NULL,
    registered timestamp without time zone NOT NULL,
    role character varying(20) DEFAULT 'user'::character varying NOT NULL
);


ALTER TABLE public.users OWNER TO xsmerda;

--
-- Data for Name: in_room; Type: TABLE DATA; Schema: public; Owner: xsmerda
--

INSERT INTO in_room VALUES (1, 16, '2019-06-24 15:33:55.268549', '2019-06-24 15:33:55.268549');


--
-- Data for Name: messages; Type: TABLE DATA; Schema: public; Owner: xsmerda
--

INSERT INTO messages VALUES (17, 14, 1, NULL, '2019-06-24 12:17:19.926824', 'Ahoj');
INSERT INTO messages VALUES (18, 14, 2, NULL, '2019-06-24 12:17:28.817246', 'Cau');
INSERT INTO messages VALUES (19, 14, 1, 2, '2019-06-24 12:17:39.323538', 'Jak se mas?');
INSERT INTO messages VALUES (20, 14, 2, 1, '2019-06-24 12:17:47.602845', 'Dobre!');


--
-- Data for Name: room_kick; Type: TABLE DATA; Schema: public; Owner: xsmerda
--



--
-- Data for Name: rooms; Type: TABLE DATA; Schema: public; Owner: xsmerda
--

INSERT INTO rooms VALUES (15, '2019-06-24 11:57:58.991851', 'Mistnost 2', 1, true);
INSERT INTO rooms VALUES (16, '2019-06-24 11:58:35.880309', 'Mistnost 3', 2, false);
INSERT INTO rooms VALUES (17, '2019-06-24 11:58:40.969011', 'Mistnost 4', 2, true);
INSERT INTO rooms VALUES (14, '2019-06-24 11:57:58.99185', 'Mistnost 1', 1, false);


--
-- Data for Name: users; Type: TABLE DATA; Schema: public; Owner: xsmerda
--

INSERT INTO users VALUES (1, 'test1', 'test1@mendelu.cz', '$2y$10$lDPrzT5XcWG5NPttQdiSueZKw9lzszHEDKJamCfZNHSCyEfVrhlrq', 'Tomas', 'Smerda', 'male', '2019-06-24 11:55:24.845157', 'user');
INSERT INTO users VALUES (2, 'test2', 'test2@mendelu.cz', '$2y$10$XuR6NJvHNCTdL.Gzs3kzluZ9WOKEOYZOR73n0h0C3DmqRd9j4igAK', 'Jana', 'Nova', 'female', '2019-06-24 11:56:18.422094', 'user');


--
-- Name: in_room_id_users_id_rooms; Type: CONSTRAINT; Schema: public; Owner: xsmerda; Tablespace: 
--

ALTER TABLE ONLY in_room
    ADD CONSTRAINT in_room_id_users_id_rooms PRIMARY KEY (id_users, id_rooms);


--
-- Name: messages_id_messages; Type: CONSTRAINT; Schema: public; Owner: xsmerda; Tablespace: 
--

ALTER TABLE ONLY messages
    ADD CONSTRAINT messages_id_messages PRIMARY KEY (id_messages);


--
-- Name: room_kick_id_users_id_rooms; Type: CONSTRAINT; Schema: public; Owner: xsmerda; Tablespace: 
--

ALTER TABLE ONLY room_kick
    ADD CONSTRAINT room_kick_id_users_id_rooms PRIMARY KEY (id_users, id_rooms);


--
-- Name: rooms_id_rooms; Type: CONSTRAINT; Schema: public; Owner: xsmerda; Tablespace: 
--

ALTER TABLE ONLY rooms
    ADD CONSTRAINT rooms_id_rooms PRIMARY KEY (id_rooms);


--
-- Name: users_email; Type: CONSTRAINT; Schema: public; Owner: xsmerda; Tablespace: 
--

ALTER TABLE ONLY users
    ADD CONSTRAINT users_email UNIQUE (email);


--
-- Name: users_id_users; Type: CONSTRAINT; Schema: public; Owner: xsmerda; Tablespace: 
--

ALTER TABLE ONLY users
    ADD CONSTRAINT users_id_users PRIMARY KEY (id_users);


--
-- Name: users_login; Type: CONSTRAINT; Schema: public; Owner: xsmerda; Tablespace: 
--

ALTER TABLE ONLY users
    ADD CONSTRAINT users_login UNIQUE (login);


--
-- Name: in_room_id_rooms; Type: INDEX; Schema: public; Owner: xsmerda; Tablespace: 
--

CREATE INDEX in_room_id_rooms ON in_room USING btree (id_rooms);


--
-- Name: in_room_id_users; Type: INDEX; Schema: public; Owner: xsmerda; Tablespace: 
--

CREATE INDEX in_room_id_users ON in_room USING btree (id_users);


--
-- Name: messages_id_rooms; Type: INDEX; Schema: public; Owner: xsmerda; Tablespace: 
--

CREATE INDEX messages_id_rooms ON messages USING btree (id_rooms);


--
-- Name: messages_id_users_from; Type: INDEX; Schema: public; Owner: xsmerda; Tablespace: 
--

CREATE INDEX messages_id_users_from ON messages USING btree (id_users_from);


--
-- Name: messages_id_users_to; Type: INDEX; Schema: public; Owner: xsmerda; Tablespace: 
--

CREATE INDEX messages_id_users_to ON messages USING btree (id_users_to);


--
-- Name: room_kick_id_rooms; Type: INDEX; Schema: public; Owner: xsmerda; Tablespace: 
--

CREATE INDEX room_kick_id_rooms ON room_kick USING btree (id_rooms);


--
-- Name: rooms_id_users_owner; Type: INDEX; Schema: public; Owner: xsmerda; Tablespace: 
--

CREATE INDEX rooms_id_users_owner ON rooms USING btree (id_users_owner);


--
-- Name: in_room_id_rooms_fkey; Type: FK CONSTRAINT; Schema: public; Owner: xsmerda
--

ALTER TABLE ONLY in_room
    ADD CONSTRAINT in_room_id_rooms_fkey FOREIGN KEY (id_rooms) REFERENCES rooms(id_rooms) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: in_room_id_users_fkey; Type: FK CONSTRAINT; Schema: public; Owner: xsmerda
--

ALTER TABLE ONLY in_room
    ADD CONSTRAINT in_room_id_users_fkey FOREIGN KEY (id_users) REFERENCES users(id_users) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: messages_id_rooms_fkey; Type: FK CONSTRAINT; Schema: public; Owner: xsmerda
--

ALTER TABLE ONLY messages
    ADD CONSTRAINT messages_id_rooms_fkey FOREIGN KEY (id_rooms) REFERENCES rooms(id_rooms) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: messages_id_users_from_fkey; Type: FK CONSTRAINT; Schema: public; Owner: xsmerda
--

ALTER TABLE ONLY messages
    ADD CONSTRAINT messages_id_users_from_fkey FOREIGN KEY (id_users_from) REFERENCES users(id_users) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: messages_id_users_to_fkey; Type: FK CONSTRAINT; Schema: public; Owner: xsmerda
--

ALTER TABLE ONLY messages
    ADD CONSTRAINT messages_id_users_to_fkey FOREIGN KEY (id_users_to) REFERENCES users(id_users) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: room_kick_id_rooms_fkey; Type: FK CONSTRAINT; Schema: public; Owner: xsmerda
--

ALTER TABLE ONLY room_kick
    ADD CONSTRAINT room_kick_id_rooms_fkey FOREIGN KEY (id_rooms) REFERENCES rooms(id_rooms) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: room_kick_id_users_fkey; Type: FK CONSTRAINT; Schema: public; Owner: xsmerda
--

ALTER TABLE ONLY room_kick
    ADD CONSTRAINT room_kick_id_users_fkey FOREIGN KEY (id_users) REFERENCES users(id_users) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: rooms_id_users_owner_fkey; Type: FK CONSTRAINT; Schema: public; Owner: xsmerda
--

ALTER TABLE ONLY rooms
    ADD CONSTRAINT rooms_id_users_owner_fkey FOREIGN KEY (id_users_owner) REFERENCES users(id_users) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: public; Type: ACL; Schema: -; Owner: postgres
--

REVOKE ALL ON SCHEMA public FROM PUBLIC;
REVOKE ALL ON SCHEMA public FROM postgres;
GRANT ALL ON SCHEMA public TO postgres;
GRANT ALL ON SCHEMA public TO PUBLIC;


--
-- PostgreSQL database dump complete
--

