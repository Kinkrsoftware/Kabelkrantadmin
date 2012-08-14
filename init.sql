--
-- PostgreSQL database dump
--

SET statement_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SET check_function_bodies = false;
SET client_min_messages = warning;

--
-- Name: plpgsql; Type: EXTENSION; Schema: -; Owner: 
--

CREATE EXTENSION IF NOT EXISTS plpgsql WITH SCHEMA pg_catalog;


--
-- Name: EXTENSION plpgsql; Type: COMMENT; Schema: -; Owner: 
--

COMMENT ON EXTENSION plpgsql IS 'PL/pgSQL procedural language';


SET search_path = public, pg_catalog;

SET default_tablespace = '';

SET default_with_oids = false;

--
-- Name: actions; Type: TABLE; Schema: public; Owner: kka; Tablespace: 
--

CREATE TABLE actions (
    id integer NOT NULL,
    action character varying(15),
    omschrijving text
);


ALTER TABLE public.actions OWNER TO kka;

--
-- Name: ads; Type: TABLE; Schema: public; Owner: kka; Tablespace: 
--

CREATE TABLE ads (
    id integer NOT NULL,
    title character varying(15),
    start integer,
    eind integer,
    betweenstart integer,
    betweeneind integer,
    frequency integer,
    state integer
);


ALTER TABLE public.ads OWNER TO kka;

--
-- Name: ads_ref; Type: TABLE; Schema: public; Owner: kka; Tablespace: 
--

CREATE TABLE ads_ref (
    id integer NOT NULL,
    type character varying(15),
    ref character varying(15),
    duration integer
);


ALTER TABLE public.ads_ref OWNER TO kka;

--
-- Name: content_seq; Type: SEQUENCE; Schema: public; Owner: kka
--

CREATE SEQUENCE content_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.content_seq OWNER TO kka;

--
-- Name: content_seq; Type: SEQUENCE SET; Schema: public; Owner: kka
--

SELECT pg_catalog.setval('content_seq', 24, true);


--
-- Name: content; Type: TABLE; Schema: public; Owner: kka; Tablespace: 
--

CREATE TABLE content (
    id integer DEFAULT nextval('content_seq'::regclass) NOT NULL,
    start integer,
    eind integer,
    state integer
);


ALTER TABLE public.content OWNER TO kka;

--
-- Name: content_category_seq; Type: SEQUENCE; Schema: public; Owner: kka
--

CREATE SEQUENCE content_category_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.content_category_seq OWNER TO kka;

--
-- Name: content_category_seq; Type: SEQUENCE SET; Schema: public; Owner: kka
--

SELECT pg_catalog.setval('content_category_seq', 5, true);


--
-- Name: content_category; Type: TABLE; Schema: public; Owner: kka; Tablespace: 
--

CREATE TABLE content_category (
    id integer DEFAULT nextval('content_category_seq'::regclass) NOT NULL,
    title character varying(15),
    idx integer
);


ALTER TABLE public.content_category OWNER TO kka;

--
-- Name: content_category_image_seq; Type: SEQUENCE; Schema: public; Owner: kka
--

CREATE SEQUENCE content_category_image_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.content_category_image_seq OWNER TO kka;

--
-- Name: content_category_image_seq; Type: SEQUENCE SET; Schema: public; Owner: kka
--

SELECT pg_catalog.setval('content_category_image_seq', 10, true);


--
-- Name: content_category_image; Type: TABLE; Schema: public; Owner: kka; Tablespace: 
--

CREATE TABLE content_category_image (
    id integer DEFAULT nextval('content_category_image_seq'::regclass) NOT NULL,
    categoryid integer,
    title character varying(20),
    photo character varying(100),
    width integer,
    height integer,
    x integer,
    y integer
);


ALTER TABLE public.content_category_image OWNER TO kka;

--
-- Name: content_category_screen; Type: TABLE; Schema: public; Owner: kka; Tablespace: 
--

CREATE TABLE content_category_screen (
    categoryid integer NOT NULL,
    screenid integer NOT NULL,
    visible boolean NOT NULL
);


ALTER TABLE public.content_category_screen OWNER TO kka;

--
-- Name: content_editor_seq; Type: SEQUENCE; Schema: public; Owner: kka
--

CREATE SEQUENCE content_editor_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.content_editor_seq OWNER TO kka;

--
-- Name: content_editor_seq; Type: SEQUENCE SET; Schema: public; Owner: kka
--

SELECT pg_catalog.setval('content_editor_seq', 1, false);


--
-- Name: content_editor; Type: TABLE; Schema: public; Owner: kka; Tablespace: 
--

CREATE TABLE content_editor (
    id integer DEFAULT nextval('content_editor_seq'::regclass) NOT NULL,
    contentid integer,
    editorid integer
);


ALTER TABLE public.content_editor OWNER TO kka;

--
-- Name: content_run_seq; Type: SEQUENCE; Schema: public; Owner: kka
--

CREATE SEQUENCE content_run_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.content_run_seq OWNER TO kka;

--
-- Name: content_run_seq; Type: SEQUENCE SET; Schema: public; Owner: kka
--

SELECT pg_catalog.setval('content_run_seq', 279, true);


--
-- Name: content_run; Type: TABLE; Schema: public; Owner: kka; Tablespace: 
--

CREATE TABLE content_run (
    id integer DEFAULT nextval('content_run_seq'::regclass) NOT NULL,
    contentid integer,
    start integer,
    eind integer,
    enabled integer,
    day smallint
);


ALTER TABLE public.content_run OWNER TO kka;

--
-- Name: content_seens; Type: TABLE; Schema: public; Owner: kka; Tablespace: 
--

CREATE TABLE content_seens (
    contentid integer NOT NULL,
    editorid integer NOT NULL
);


ALTER TABLE public.content_seens OWNER TO kka;

--
-- Name: content_text_seq; Type: SEQUENCE; Schema: public; Owner: kka
--

CREATE SEQUENCE content_text_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.content_text_seq OWNER TO kka;

--
-- Name: content_text_seq; Type: SEQUENCE SET; Schema: public; Owner: kka
--

SELECT pg_catalog.setval('content_text_seq', 551, true);


--
-- Name: content_text; Type: TABLE; Schema: public; Owner: kka; Tablespace: 
--

CREATE TABLE content_text (
    id integer DEFAULT nextval('content_text_seq'::regclass) NOT NULL,
    contentid integer,
    template character varying(50),
    category integer,
    duration integer,
    photo character varying(100),
    title character varying(128),
    content text
);


ALTER TABLE public.content_text OWNER TO kka;

--
-- Name: editors_seq; Type: SEQUENCE; Schema: public; Owner: kka
--

CREATE SEQUENCE editors_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.editors_seq OWNER TO kka;

--
-- Name: editors_seq; Type: SEQUENCE SET; Schema: public; Owner: kka
--

SELECT pg_catalog.setval('editors_seq', 1, false);


--
-- Name: editors; Type: TABLE; Schema: public; Owner: kka; Tablespace: 
--

CREATE TABLE editors (
    id integer DEFAULT nextval('editors_seq'::regclass) NOT NULL,
    login character varying(15),
    passphrase character varying(32),
    surname character varying(50),
    addictions character varying(10),
    givenname character varying(50)
);


ALTER TABLE public.editors OWNER TO kka;

--
-- Name: rights; Type: TABLE; Schema: public; Owner: kka; Tablespace: 
--

CREATE TABLE rights (
    id integer NOT NULL,
    editorid integer,
    actionid integer,
    contentid integer
);


ALTER TABLE public.rights OWNER TO kka;

--
-- Name: screen; Type: TABLE; Schema: public; Owner: kka; Tablespace: 
--

CREATE TABLE screen (
    id integer NOT NULL,
    name character varying(255),
    location character varying(255),
    ip character varying(16)
);


ALTER TABLE public.screen OWNER TO kka;

--
-- Name: screen_id_seq; Type: SEQUENCE; Schema: public; Owner: kka
--

CREATE SEQUENCE screen_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.screen_id_seq OWNER TO kka;

--
-- Name: screen_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: kka
--

ALTER SEQUENCE screen_id_seq OWNED BY screen.id;


--
-- Name: screen_id_seq; Type: SEQUENCE SET; Schema: public; Owner: kka
--

SELECT pg_catalog.setval('screen_id_seq', 5, true);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: kka
--

ALTER TABLE ONLY screen ALTER COLUMN id SET DEFAULT nextval('screen_id_seq'::regclass);


--
-- Name: actions_action_key; Type: CONSTRAINT; Schema: public; Owner: kka; Tablespace: 
--

ALTER TABLE ONLY actions
    ADD CONSTRAINT actions_action_key UNIQUE (action);


--
-- Name: actions_pkey; Type: CONSTRAINT; Schema: public; Owner: kka; Tablespace: 
--

ALTER TABLE ONLY actions
    ADD CONSTRAINT actions_pkey PRIMARY KEY (id);


--
-- Name: ads_pkey; Type: CONSTRAINT; Schema: public; Owner: kka; Tablespace: 
--

ALTER TABLE ONLY ads
    ADD CONSTRAINT ads_pkey PRIMARY KEY (id);


--
-- Name: ads_ref_pkey; Type: CONSTRAINT; Schema: public; Owner: kka; Tablespace: 
--

ALTER TABLE ONLY ads_ref
    ADD CONSTRAINT ads_ref_pkey PRIMARY KEY (id);


--
-- Name: content_category_image_pkey; Type: CONSTRAINT; Schema: public; Owner: kka; Tablespace: 
--

ALTER TABLE ONLY content_category_image
    ADD CONSTRAINT content_category_image_pkey PRIMARY KEY (id);


--
-- Name: content_category_pkey; Type: CONSTRAINT; Schema: public; Owner: kka; Tablespace: 
--

ALTER TABLE ONLY content_category
    ADD CONSTRAINT content_category_pkey PRIMARY KEY (id);


--
-- Name: content_category_screen_pkey; Type: CONSTRAINT; Schema: public; Owner: kka; Tablespace: 
--

ALTER TABLE ONLY content_category_screen
    ADD CONSTRAINT content_category_screen_pkey PRIMARY KEY (categoryid, screenid);


--
-- Name: content_editor_pkey; Type: CONSTRAINT; Schema: public; Owner: kka; Tablespace: 
--

ALTER TABLE ONLY content_editor
    ADD CONSTRAINT content_editor_pkey PRIMARY KEY (id);


--
-- Name: content_pkey; Type: CONSTRAINT; Schema: public; Owner: kka; Tablespace: 
--

ALTER TABLE ONLY content
    ADD CONSTRAINT content_pkey PRIMARY KEY (id);


--
-- Name: content_run_pkey; Type: CONSTRAINT; Schema: public; Owner: kka; Tablespace: 
--

ALTER TABLE ONLY content_run
    ADD CONSTRAINT content_run_pkey PRIMARY KEY (id);


--
-- Name: content_seens_pkey; Type: CONSTRAINT; Schema: public; Owner: kka; Tablespace: 
--

ALTER TABLE ONLY content_seens
    ADD CONSTRAINT content_seens_pkey PRIMARY KEY (contentid, editorid);


--
-- Name: content_text_pkey; Type: CONSTRAINT; Schema: public; Owner: kka; Tablespace: 
--

ALTER TABLE ONLY content_text
    ADD CONSTRAINT content_text_pkey PRIMARY KEY (id);


--
-- Name: editors_login_key; Type: CONSTRAINT; Schema: public; Owner: kka; Tablespace: 
--

ALTER TABLE ONLY editors
    ADD CONSTRAINT editors_login_key UNIQUE (login);


--
-- Name: editors_pkey; Type: CONSTRAINT; Schema: public; Owner: kka; Tablespace: 
--

ALTER TABLE ONLY editors
    ADD CONSTRAINT editors_pkey PRIMARY KEY (id);


--
-- Name: rights_pkey; Type: CONSTRAINT; Schema: public; Owner: kka; Tablespace: 
--

ALTER TABLE ONLY rights
    ADD CONSTRAINT rights_pkey PRIMARY KEY (id);


--
-- Name: screen_pkey; Type: CONSTRAINT; Schema: public; Owner: kka; Tablespace: 
--

ALTER TABLE ONLY screen
    ADD CONSTRAINT screen_pkey PRIMARY KEY (id);


--
-- Name: unique_category_title; Type: CONSTRAINT; Schema: public; Owner: kka; Tablespace: 
--

ALTER TABLE ONLY content_category
    ADD CONSTRAINT unique_category_title UNIQUE (title);


--
-- Name: content_category_image_categoryid_fkey; Type: FK CONSTRAINT; Schema: public; Owner: kka
--

ALTER TABLE ONLY content_category_image
    ADD CONSTRAINT content_category_image_categoryid_fkey FOREIGN KEY (categoryid) REFERENCES content_category(id);


--
-- Name: content_category_screen_categoryid_fkey; Type: FK CONSTRAINT; Schema: public; Owner: kka
--

ALTER TABLE ONLY content_category_screen
    ADD CONSTRAINT content_category_screen_categoryid_fkey FOREIGN KEY (categoryid) REFERENCES content_category(id);


--
-- Name: content_category_screen_screenid_fkey; Type: FK CONSTRAINT; Schema: public; Owner: kka
--

ALTER TABLE ONLY content_category_screen
    ADD CONSTRAINT content_category_screen_screenid_fkey FOREIGN KEY (screenid) REFERENCES screen(id);


--
-- Name: content_editor_contentid_fkey; Type: FK CONSTRAINT; Schema: public; Owner: kka
--

ALTER TABLE ONLY content_editor
    ADD CONSTRAINT content_editor_contentid_fkey FOREIGN KEY (contentid) REFERENCES content(id);


--
-- Name: content_editor_editorid_fkey; Type: FK CONSTRAINT; Schema: public; Owner: kka
--

ALTER TABLE ONLY content_editor
    ADD CONSTRAINT content_editor_editorid_fkey FOREIGN KEY (editorid) REFERENCES editors(id);


--
-- Name: content_run_contentid_fkey; Type: FK CONSTRAINT; Schema: public; Owner: kka
--

ALTER TABLE ONLY content_run
    ADD CONSTRAINT content_run_contentid_fkey FOREIGN KEY (contentid) REFERENCES content(id);


--
-- Name: content_seens_contentid_fkey; Type: FK CONSTRAINT; Schema: public; Owner: kka
--

ALTER TABLE ONLY content_seens
    ADD CONSTRAINT content_seens_contentid_fkey FOREIGN KEY (contentid) REFERENCES content(id);


--
-- Name: content_seens_editorid_fkey; Type: FK CONSTRAINT; Schema: public; Owner: kka
--

ALTER TABLE ONLY content_seens
    ADD CONSTRAINT content_seens_editorid_fkey FOREIGN KEY (editorid) REFERENCES content_editor(id);


--
-- Name: content_text_category_fkey; Type: FK CONSTRAINT; Schema: public; Owner: kka
--

ALTER TABLE ONLY content_text
    ADD CONSTRAINT content_text_category_fkey FOREIGN KEY (category) REFERENCES content_category_image(id);


--
-- Name: content_text_contentid_fkey; Type: FK CONSTRAINT; Schema: public; Owner: kka
--

ALTER TABLE ONLY content_text
    ADD CONSTRAINT content_text_contentid_fkey FOREIGN KEY (contentid) REFERENCES content(id);


--
-- Name: rights_actionid_fkey; Type: FK CONSTRAINT; Schema: public; Owner: kka
--

ALTER TABLE ONLY rights
    ADD CONSTRAINT rights_actionid_fkey FOREIGN KEY (actionid) REFERENCES actions(id);


--
-- Name: rights_contentid_fkey; Type: FK CONSTRAINT; Schema: public; Owner: kka
--

ALTER TABLE ONLY rights
    ADD CONSTRAINT rights_contentid_fkey FOREIGN KEY (contentid) REFERENCES content(id);


--
-- Name: rights_editorid_fkey; Type: FK CONSTRAINT; Schema: public; Owner: kka
--

ALTER TABLE ONLY rights
    ADD CONSTRAINT rights_editorid_fkey FOREIGN KEY (editorid) REFERENCES editors(id);


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

