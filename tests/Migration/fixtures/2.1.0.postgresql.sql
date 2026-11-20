--
-- PostgreSQL database dump
--

\restrict V2Qe4tcY83q0eNhb2g1tuCcK8E9FcvWUiz2HZM1H29QmqLVW5BU7sKWhsvbhgnh

-- Dumped from database version 16.12 (Debian 16.12-1.pgdg13+1)
-- Dumped by pg_dump version 16.12 (Debian 16.12-1.pgdg13+1)

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET xmloption = content;
SET client_min_messages = warning;
SET row_security = off;

SET default_tablespace = '';

SET default_table_access_method = heap;

--
-- Name: wt_block; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.wt_block (
    block_id integer NOT NULL,
    gedcom_id integer,
    user_id integer,
    xref character varying(20),
    location character varying(255),
    block_order integer NOT NULL,
    module_name character varying(32) NOT NULL,
    CONSTRAINT wt_block_location_check CHECK (((location)::text = ANY ((ARRAY['main'::character varying, 'side'::character varying])::text[])))
);


--
-- Name: wt_block_block_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.wt_block_block_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: wt_block_block_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.wt_block_block_id_seq OWNED BY public.wt_block.block_id;


--
-- Name: wt_block_setting; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.wt_block_setting (
    block_id integer NOT NULL,
    setting_name character varying(32) NOT NULL,
    setting_value text NOT NULL
);


--
-- Name: wt_change; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.wt_change (
    change_id integer NOT NULL,
    change_time timestamp(0) without time zone DEFAULT CURRENT_TIMESTAMP NOT NULL,
    status character varying(255) DEFAULT 'pending'::character varying NOT NULL,
    gedcom_id integer NOT NULL,
    xref character varying(20) NOT NULL,
    old_gedcom text NOT NULL,
    new_gedcom text NOT NULL,
    user_id integer NOT NULL,
    CONSTRAINT wt_change_status_check CHECK (((status)::text = ANY ((ARRAY['accepted'::character varying, 'pending'::character varying, 'rejected'::character varying])::text[])))
);


--
-- Name: wt_change_change_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.wt_change_change_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: wt_change_change_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.wt_change_change_id_seq OWNED BY public.wt_change.change_id;


--
-- Name: wt_dates; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.wt_dates (
    d_day smallint NOT NULL,
    d_month character(5),
    d_mon smallint NOT NULL,
    d_year smallint NOT NULL,
    d_julianday1 integer NOT NULL,
    d_julianday2 integer NOT NULL,
    d_fact character varying(15) NOT NULL,
    d_gid character varying(20) NOT NULL,
    d_file integer NOT NULL,
    d_type character varying(255) NOT NULL,
    CONSTRAINT wt_dates_d_type_check CHECK (((d_type)::text = ANY ((ARRAY['@#DGREGORIAN@'::character varying, '@#DJULIAN@'::character varying, '@#DHEBREW@'::character varying, '@#DFRENCH R@'::character varying, '@#DHIJRI@'::character varying, '@#DROMAN@'::character varying, '@#DJALALI@'::character varying])::text[])))
);


--
-- Name: wt_default_resn; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.wt_default_resn (
    default_resn_id integer NOT NULL,
    gedcom_id integer NOT NULL,
    xref character varying(20),
    tag_type character varying(15),
    resn character varying(255) NOT NULL,
    comment character varying(255),
    updated timestamp(0) without time zone DEFAULT CURRENT_TIMESTAMP NOT NULL,
    CONSTRAINT wt_default_resn_resn_check CHECK (((resn)::text = ANY ((ARRAY['none'::character varying, 'privacy'::character varying, 'confidential'::character varying, 'hidden'::character varying])::text[])))
);


--
-- Name: wt_default_resn_default_resn_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.wt_default_resn_default_resn_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: wt_default_resn_default_resn_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.wt_default_resn_default_resn_id_seq OWNED BY public.wt_default_resn.default_resn_id;


--
-- Name: wt_families; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.wt_families (
    f_id character varying(20) NOT NULL,
    f_file integer NOT NULL,
    f_husb character varying(20),
    f_wife character varying(20),
    f_gedcom text NOT NULL,
    f_numchil integer NOT NULL
);


--
-- Name: wt_favorite; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.wt_favorite (
    favorite_id integer NOT NULL,
    user_id integer,
    gedcom_id integer NOT NULL,
    xref character varying(20),
    favorite_type character varying(255) NOT NULL,
    url character varying(255),
    title character varying(255),
    note character varying(1000),
    CONSTRAINT wt_favorite_favorite_type_check CHECK (((favorite_type)::text = ANY ((ARRAY['INDI'::character varying, 'FAM'::character varying, 'SOUR'::character varying, 'REPO'::character varying, 'OBJE'::character varying, 'NOTE'::character varying, 'URL'::character varying])::text[])))
);


--
-- Name: wt_favorite_favorite_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.wt_favorite_favorite_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: wt_favorite_favorite_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.wt_favorite_favorite_id_seq OWNED BY public.wt_favorite.favorite_id;


--
-- Name: wt_gedcom; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.wt_gedcom (
    gedcom_id integer NOT NULL,
    gedcom_name character varying(255) NOT NULL,
    sort_order integer DEFAULT 0 NOT NULL
);


--
-- Name: wt_gedcom_chunk; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.wt_gedcom_chunk (
    gedcom_chunk_id integer NOT NULL,
    gedcom_id integer NOT NULL,
    chunk_data bytea NOT NULL,
    imported boolean DEFAULT false NOT NULL
);


--
-- Name: wt_gedcom_chunk_gedcom_chunk_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.wt_gedcom_chunk_gedcom_chunk_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: wt_gedcom_chunk_gedcom_chunk_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.wt_gedcom_chunk_gedcom_chunk_id_seq OWNED BY public.wt_gedcom_chunk.gedcom_chunk_id;


--
-- Name: wt_gedcom_gedcom_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.wt_gedcom_gedcom_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: wt_gedcom_gedcom_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.wt_gedcom_gedcom_id_seq OWNED BY public.wt_gedcom.gedcom_id;


--
-- Name: wt_gedcom_setting; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.wt_gedcom_setting (
    gedcom_id integer NOT NULL,
    setting_name character varying(32) NOT NULL,
    setting_value character varying(255) NOT NULL
);


--
-- Name: wt_hit_counter; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.wt_hit_counter (
    gedcom_id integer NOT NULL,
    page_name character varying(32) NOT NULL,
    page_parameter character varying(32) NOT NULL,
    page_count integer NOT NULL
);


--
-- Name: wt_individuals; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.wt_individuals (
    i_id character varying(20) NOT NULL,
    i_file integer NOT NULL,
    i_rin character varying(20) NOT NULL,
    i_sex character varying(255) NOT NULL,
    i_gedcom text NOT NULL,
    CONSTRAINT wt_individuals_i_sex_check CHECK (((i_sex)::text = ANY ((ARRAY['U'::character varying, 'M'::character varying, 'F'::character varying])::text[])))
);


--
-- Name: wt_link; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.wt_link (
    l_file integer NOT NULL,
    l_from character varying(20) NOT NULL,
    l_type character varying(15) NOT NULL,
    l_to character varying(20) NOT NULL
);


--
-- Name: wt_log; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.wt_log (
    log_id integer NOT NULL,
    log_time timestamp(0) without time zone DEFAULT CURRENT_TIMESTAMP NOT NULL,
    log_type character varying(255) NOT NULL,
    log_message text NOT NULL,
    ip_address inet NOT NULL,
    user_id integer,
    gedcom_id integer,
    CONSTRAINT wt_log_log_type_check CHECK (((log_type)::text = ANY ((ARRAY['auth'::character varying, 'config'::character varying, 'debug'::character varying, 'edit'::character varying, 'error'::character varying, 'media'::character varying, 'search'::character varying])::text[])))
);


--
-- Name: wt_log_log_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.wt_log_log_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: wt_log_log_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.wt_log_log_id_seq OWNED BY public.wt_log.log_id;


--
-- Name: wt_media; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.wt_media (
    m_id character varying(20) NOT NULL,
    m_file integer NOT NULL,
    m_gedcom text
);


--
-- Name: wt_media_file; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.wt_media_file (
    id integer NOT NULL,
    m_id character varying(20) NOT NULL,
    m_file integer NOT NULL,
    multimedia_file_refn character varying(248) NOT NULL,
    multimedia_format character varying(4) NOT NULL,
    source_media_type character varying(15) NOT NULL,
    descriptive_title character varying(248) NOT NULL
);


--
-- Name: wt_media_file_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.wt_media_file_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: wt_media_file_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.wt_media_file_id_seq OWNED BY public.wt_media_file.id;


--
-- Name: wt_message; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.wt_message (
    message_id integer NOT NULL,
    sender character varying(64) NOT NULL,
    ip_address inet NOT NULL,
    user_id integer NOT NULL,
    subject character varying(255) NOT NULL,
    body text NOT NULL,
    created timestamp(0) without time zone DEFAULT CURRENT_TIMESTAMP NOT NULL
);


--
-- Name: wt_message_message_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.wt_message_message_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: wt_message_message_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.wt_message_message_id_seq OWNED BY public.wt_message.message_id;


--
-- Name: wt_module; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.wt_module (
    module_name character varying(32) NOT NULL,
    status character varying(255) DEFAULT 'enabled'::character varying NOT NULL,
    tab_order integer,
    menu_order integer,
    sidebar_order integer,
    footer_order integer,
    CONSTRAINT wt_module_status_check CHECK (((status)::text = ANY ((ARRAY['enabled'::character varying, 'disabled'::character varying])::text[])))
);


--
-- Name: wt_module_privacy; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.wt_module_privacy (
    id integer NOT NULL,
    module_name character varying(32) NOT NULL,
    gedcom_id integer NOT NULL,
    interface character varying(255) NOT NULL,
    access_level smallint NOT NULL
);


--
-- Name: wt_module_privacy_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.wt_module_privacy_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: wt_module_privacy_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.wt_module_privacy_id_seq OWNED BY public.wt_module_privacy.id;


--
-- Name: wt_module_setting; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.wt_module_setting (
    module_name character varying(32) NOT NULL,
    setting_name character varying(32) NOT NULL,
    setting_value text NOT NULL
);


--
-- Name: wt_name; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.wt_name (
    n_file integer NOT NULL,
    n_id character varying(20) NOT NULL,
    n_num integer NOT NULL,
    n_type character varying(15) NOT NULL,
    n_sort character varying(255) NOT NULL,
    n_full character varying(255) NOT NULL,
    n_surname character varying(255),
    n_surn character varying(255),
    n_givn character varying(255),
    n_soundex_givn_std character varying(255),
    n_soundex_surn_std character varying(255),
    n_soundex_givn_dm character varying(255),
    n_soundex_surn_dm character varying(255)
);


--
-- Name: wt_news; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.wt_news (
    news_id integer NOT NULL,
    user_id integer,
    gedcom_id integer,
    subject character varying(255) NOT NULL,
    body text NOT NULL,
    updated timestamp(0) without time zone DEFAULT CURRENT_TIMESTAMP NOT NULL
);


--
-- Name: wt_news_news_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.wt_news_news_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: wt_news_news_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.wt_news_news_id_seq OWNED BY public.wt_news.news_id;


--
-- Name: wt_other; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.wt_other (
    o_id character varying(20) NOT NULL,
    o_file integer NOT NULL,
    o_type character varying(15) NOT NULL,
    o_gedcom text NOT NULL
);


--
-- Name: wt_place_location; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.wt_place_location (
    id integer NOT NULL,
    parent_id integer,
    place character varying(120) NOT NULL,
    latitude double precision,
    longitude double precision
);


--
-- Name: wt_place_location_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.wt_place_location_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: wt_place_location_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.wt_place_location_id_seq OWNED BY public.wt_place_location.id;


--
-- Name: wt_placelinks; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.wt_placelinks (
    pl_p_id integer NOT NULL,
    pl_gid character varying(20) NOT NULL,
    pl_file integer NOT NULL
);


--
-- Name: wt_places; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.wt_places (
    p_id integer NOT NULL,
    p_place character varying(150) NOT NULL,
    p_parent_id integer,
    p_file integer NOT NULL,
    p_std_soundex text,
    p_dm_soundex text
);


--
-- Name: wt_places_p_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.wt_places_p_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: wt_places_p_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.wt_places_p_id_seq OWNED BY public.wt_places.p_id;


--
-- Name: wt_session; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.wt_session (
    session_id character varying(32) NOT NULL,
    session_time timestamp(0) without time zone DEFAULT CURRENT_TIMESTAMP NOT NULL,
    user_id integer NOT NULL,
    ip_address inet NOT NULL,
    session_data bytea NOT NULL
);


--
-- Name: wt_site_setting; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.wt_site_setting (
    setting_name character varying(32) NOT NULL,
    setting_value character varying(2000) NOT NULL
);


--
-- Name: wt_sources; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.wt_sources (
    s_id character varying(20) NOT NULL,
    s_file integer NOT NULL,
    s_name character varying(255) NOT NULL,
    s_gedcom text NOT NULL
);


--
-- Name: wt_user; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.wt_user (
    user_id integer NOT NULL,
    user_name character varying(32) NOT NULL,
    real_name character varying(64) NOT NULL,
    email character varying(64) NOT NULL,
    password character varying(128) NOT NULL
);


--
-- Name: wt_user_gedcom_setting; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.wt_user_gedcom_setting (
    user_id integer NOT NULL,
    gedcom_id integer NOT NULL,
    setting_name character varying(32) NOT NULL,
    setting_value character varying(255) NOT NULL
);


--
-- Name: wt_user_setting; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.wt_user_setting (
    user_id integer NOT NULL,
    setting_name character varying(32) NOT NULL,
    setting_value character varying(255) NOT NULL
);


--
-- Name: wt_user_user_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.wt_user_user_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: wt_user_user_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.wt_user_user_id_seq OWNED BY public.wt_user.user_id;


--
-- Name: wt_block block_id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.wt_block ALTER COLUMN block_id SET DEFAULT nextval('public.wt_block_block_id_seq'::regclass);


--
-- Name: wt_change change_id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.wt_change ALTER COLUMN change_id SET DEFAULT nextval('public.wt_change_change_id_seq'::regclass);


--
-- Name: wt_default_resn default_resn_id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.wt_default_resn ALTER COLUMN default_resn_id SET DEFAULT nextval('public.wt_default_resn_default_resn_id_seq'::regclass);


--
-- Name: wt_favorite favorite_id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.wt_favorite ALTER COLUMN favorite_id SET DEFAULT nextval('public.wt_favorite_favorite_id_seq'::regclass);


--
-- Name: wt_gedcom gedcom_id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.wt_gedcom ALTER COLUMN gedcom_id SET DEFAULT nextval('public.wt_gedcom_gedcom_id_seq'::regclass);


--
-- Name: wt_gedcom_chunk gedcom_chunk_id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.wt_gedcom_chunk ALTER COLUMN gedcom_chunk_id SET DEFAULT nextval('public.wt_gedcom_chunk_gedcom_chunk_id_seq'::regclass);


--
-- Name: wt_log log_id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.wt_log ALTER COLUMN log_id SET DEFAULT nextval('public.wt_log_log_id_seq'::regclass);


--
-- Name: wt_media_file id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.wt_media_file ALTER COLUMN id SET DEFAULT nextval('public.wt_media_file_id_seq'::regclass);


--
-- Name: wt_message message_id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.wt_message ALTER COLUMN message_id SET DEFAULT nextval('public.wt_message_message_id_seq'::regclass);


--
-- Name: wt_module_privacy id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.wt_module_privacy ALTER COLUMN id SET DEFAULT nextval('public.wt_module_privacy_id_seq'::regclass);


--
-- Name: wt_news news_id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.wt_news ALTER COLUMN news_id SET DEFAULT nextval('public.wt_news_news_id_seq'::regclass);


--
-- Name: wt_place_location id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.wt_place_location ALTER COLUMN id SET DEFAULT nextval('public.wt_place_location_id_seq'::regclass);


--
-- Name: wt_places p_id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.wt_places ALTER COLUMN p_id SET DEFAULT nextval('public.wt_places_p_id_seq'::regclass);


--
-- Name: wt_user user_id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.wt_user ALTER COLUMN user_id SET DEFAULT nextval('public.wt_user_user_id_seq'::regclass);


--
-- Name: wt_default_resn default_resn_gedcom_id_xref_tag_type_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.wt_default_resn
    ADD CONSTRAINT default_resn_gedcom_id_xref_tag_type_unique UNIQUE (gedcom_id, xref, tag_type);


--
-- Name: wt_families families_f_file_f_id_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.wt_families
    ADD CONSTRAINT families_f_file_f_id_unique UNIQUE (f_file, f_id);


--
-- Name: wt_gedcom gedcom_gedcom_name_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.wt_gedcom
    ADD CONSTRAINT gedcom_gedcom_name_unique UNIQUE (gedcom_name);


--
-- Name: wt_individuals individuals_i_file_i_id_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.wt_individuals
    ADD CONSTRAINT individuals_i_file_i_id_unique UNIQUE (i_file, i_id);


--
-- Name: wt_link link_l_to_l_file_l_type_l_from_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.wt_link
    ADD CONSTRAINT link_l_to_l_file_l_type_l_from_unique UNIQUE (l_to, l_file, l_type, l_from);


--
-- Name: wt_media media_m_id_m_file_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.wt_media
    ADD CONSTRAINT media_m_id_m_file_unique UNIQUE (m_id, m_file);


--
-- Name: wt_other other_o_file_o_id_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.wt_other
    ADD CONSTRAINT other_o_file_o_id_unique UNIQUE (o_file, o_id);


--
-- Name: wt_place_location place_location_parent_id_place_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.wt_place_location
    ADD CONSTRAINT place_location_parent_id_place_unique UNIQUE (parent_id, place);


--
-- Name: wt_place_location place_location_place_parent_id_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.wt_place_location
    ADD CONSTRAINT place_location_place_parent_id_unique UNIQUE (place, parent_id);


--
-- Name: wt_places places_p_parent_id_p_file_p_place_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.wt_places
    ADD CONSTRAINT places_p_parent_id_p_file_p_place_unique UNIQUE (p_parent_id, p_file, p_place);


--
-- Name: wt_sources sources_s_file_s_id_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.wt_sources
    ADD CONSTRAINT sources_s_file_s_id_unique UNIQUE (s_file, s_id);


--
-- Name: wt_user user_email_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.wt_user
    ADD CONSTRAINT user_email_unique UNIQUE (email);


--
-- Name: wt_user user_user_name_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.wt_user
    ADD CONSTRAINT user_user_name_unique UNIQUE (user_name);


--
-- Name: wt_block wt_block_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.wt_block
    ADD CONSTRAINT wt_block_pkey PRIMARY KEY (block_id);


--
-- Name: wt_block_setting wt_block_setting_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.wt_block_setting
    ADD CONSTRAINT wt_block_setting_pkey PRIMARY KEY (block_id, setting_name);


--
-- Name: wt_change wt_change_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.wt_change
    ADD CONSTRAINT wt_change_pkey PRIMARY KEY (change_id);


--
-- Name: wt_default_resn wt_default_resn_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.wt_default_resn
    ADD CONSTRAINT wt_default_resn_pkey PRIMARY KEY (default_resn_id);


--
-- Name: wt_families wt_families_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.wt_families
    ADD CONSTRAINT wt_families_pkey PRIMARY KEY (f_id, f_file);


--
-- Name: wt_favorite wt_favorite_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.wt_favorite
    ADD CONSTRAINT wt_favorite_pkey PRIMARY KEY (favorite_id);


--
-- Name: wt_gedcom_chunk wt_gedcom_chunk_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.wt_gedcom_chunk
    ADD CONSTRAINT wt_gedcom_chunk_pkey PRIMARY KEY (gedcom_chunk_id);


--
-- Name: wt_gedcom wt_gedcom_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.wt_gedcom
    ADD CONSTRAINT wt_gedcom_pkey PRIMARY KEY (gedcom_id);


--
-- Name: wt_gedcom_setting wt_gedcom_setting_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.wt_gedcom_setting
    ADD CONSTRAINT wt_gedcom_setting_pkey PRIMARY KEY (gedcom_id, setting_name);


--
-- Name: wt_hit_counter wt_hit_counter_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.wt_hit_counter
    ADD CONSTRAINT wt_hit_counter_pkey PRIMARY KEY (gedcom_id, page_name, page_parameter);


--
-- Name: wt_individuals wt_individuals_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.wt_individuals
    ADD CONSTRAINT wt_individuals_pkey PRIMARY KEY (i_id, i_file);


--
-- Name: wt_link wt_link_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.wt_link
    ADD CONSTRAINT wt_link_pkey PRIMARY KEY (l_from, l_file, l_type, l_to);


--
-- Name: wt_log wt_log_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.wt_log
    ADD CONSTRAINT wt_log_pkey PRIMARY KEY (log_id);


--
-- Name: wt_media_file wt_media_file_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.wt_media_file
    ADD CONSTRAINT wt_media_file_pkey PRIMARY KEY (id);


--
-- Name: wt_media wt_media_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.wt_media
    ADD CONSTRAINT wt_media_pkey PRIMARY KEY (m_file, m_id);


--
-- Name: wt_message wt_message_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.wt_message
    ADD CONSTRAINT wt_message_pkey PRIMARY KEY (message_id);


--
-- Name: wt_module wt_module_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.wt_module
    ADD CONSTRAINT wt_module_pkey PRIMARY KEY (module_name);


--
-- Name: wt_module_privacy wt_module_privacy_ix1; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.wt_module_privacy
    ADD CONSTRAINT wt_module_privacy_ix1 UNIQUE (gedcom_id, module_name, interface);


--
-- Name: wt_module_privacy wt_module_privacy_ix2; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.wt_module_privacy
    ADD CONSTRAINT wt_module_privacy_ix2 UNIQUE (module_name, gedcom_id, interface);


--
-- Name: wt_module_privacy wt_module_privacy_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.wt_module_privacy
    ADD CONSTRAINT wt_module_privacy_pkey PRIMARY KEY (id);


--
-- Name: wt_module_setting wt_module_setting_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.wt_module_setting
    ADD CONSTRAINT wt_module_setting_pkey PRIMARY KEY (module_name, setting_name);


--
-- Name: wt_name wt_name_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.wt_name
    ADD CONSTRAINT wt_name_pkey PRIMARY KEY (n_id, n_file, n_num);


--
-- Name: wt_news wt_news_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.wt_news
    ADD CONSTRAINT wt_news_pkey PRIMARY KEY (news_id);


--
-- Name: wt_other wt_other_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.wt_other
    ADD CONSTRAINT wt_other_pkey PRIMARY KEY (o_id, o_file);


--
-- Name: wt_place_location wt_place_location_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.wt_place_location
    ADD CONSTRAINT wt_place_location_pkey PRIMARY KEY (id);


--
-- Name: wt_placelinks wt_placelinks_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.wt_placelinks
    ADD CONSTRAINT wt_placelinks_pkey PRIMARY KEY (pl_p_id, pl_gid, pl_file);


--
-- Name: wt_places wt_places_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.wt_places
    ADD CONSTRAINT wt_places_pkey PRIMARY KEY (p_id);


--
-- Name: wt_session wt_session_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.wt_session
    ADD CONSTRAINT wt_session_pkey PRIMARY KEY (session_id);


--
-- Name: wt_site_setting wt_site_setting_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.wt_site_setting
    ADD CONSTRAINT wt_site_setting_pkey PRIMARY KEY (setting_name);


--
-- Name: wt_sources wt_sources_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.wt_sources
    ADD CONSTRAINT wt_sources_pkey PRIMARY KEY (s_id, s_file);


--
-- Name: wt_user_gedcom_setting wt_user_gedcom_setting_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.wt_user_gedcom_setting
    ADD CONSTRAINT wt_user_gedcom_setting_pkey PRIMARY KEY (user_id, gedcom_id, setting_name);


--
-- Name: wt_user wt_user_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.wt_user
    ADD CONSTRAINT wt_user_pkey PRIMARY KEY (user_id);


--
-- Name: wt_user_setting wt_user_setting_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.wt_user_setting
    ADD CONSTRAINT wt_user_setting_pkey PRIMARY KEY (user_id, setting_name);


--
-- Name: block_gedcom_id_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX block_gedcom_id_index ON public.wt_block USING btree (gedcom_id);


--
-- Name: block_module_name_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX block_module_name_index ON public.wt_block USING btree (module_name);


--
-- Name: block_user_id_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX block_user_id_index ON public.wt_block USING btree (user_id);


--
-- Name: change_gedcom_id_status_xref_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX change_gedcom_id_status_xref_index ON public.wt_change USING btree (gedcom_id, status, xref);


--
-- Name: change_user_id_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX change_user_id_index ON public.wt_change USING btree (user_id);


--
-- Name: dates_d_day_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX dates_d_day_index ON public.wt_dates USING btree (d_day);


--
-- Name: dates_d_fact_d_gid_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX dates_d_fact_d_gid_index ON public.wt_dates USING btree (d_fact, d_gid);


--
-- Name: dates_d_file_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX dates_d_file_index ON public.wt_dates USING btree (d_file);


--
-- Name: dates_d_gid_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX dates_d_gid_index ON public.wt_dates USING btree (d_gid);


--
-- Name: dates_d_julianday1_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX dates_d_julianday1_index ON public.wt_dates USING btree (d_julianday1);


--
-- Name: dates_d_julianday2_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX dates_d_julianday2_index ON public.wt_dates USING btree (d_julianday2);


--
-- Name: dates_d_mon_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX dates_d_mon_index ON public.wt_dates USING btree (d_mon);


--
-- Name: dates_d_month_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX dates_d_month_index ON public.wt_dates USING btree (d_month);


--
-- Name: dates_d_type_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX dates_d_type_index ON public.wt_dates USING btree (d_type);


--
-- Name: dates_d_year_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX dates_d_year_index ON public.wt_dates USING btree (d_year);


--
-- Name: families_f_husb_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX families_f_husb_index ON public.wt_families USING btree (f_husb);


--
-- Name: families_f_wife_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX families_f_wife_index ON public.wt_families USING btree (f_wife);


--
-- Name: favorite_gedcom_id_user_id_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX favorite_gedcom_id_user_id_index ON public.wt_favorite USING btree (gedcom_id, user_id);


--
-- Name: favorite_user_id_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX favorite_user_id_index ON public.wt_favorite USING btree (user_id);


--
-- Name: gedcom_chunk_gedcom_id_imported_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX gedcom_chunk_gedcom_id_imported_index ON public.wt_gedcom_chunk USING btree (gedcom_id, imported);


--
-- Name: gedcom_sort_order_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX gedcom_sort_order_index ON public.wt_gedcom USING btree (sort_order);


--
-- Name: log_gedcom_id_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX log_gedcom_id_index ON public.wt_log USING btree (gedcom_id);


--
-- Name: log_ip_address_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX log_ip_address_index ON public.wt_log USING btree (ip_address);


--
-- Name: log_log_time_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX log_log_time_index ON public.wt_log USING btree (log_time);


--
-- Name: log_log_type_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX log_log_type_index ON public.wt_log USING btree (log_type);


--
-- Name: log_user_id_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX log_user_id_index ON public.wt_log USING btree (user_id);


--
-- Name: media_file_m_file_descriptive_title_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX media_file_m_file_descriptive_title_index ON public.wt_media_file USING btree (m_file, descriptive_title);


--
-- Name: media_file_m_file_m_id_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX media_file_m_file_m_id_index ON public.wt_media_file USING btree (m_file, m_id);


--
-- Name: media_file_m_file_multimedia_file_refn_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX media_file_m_file_multimedia_file_refn_index ON public.wt_media_file USING btree (m_file, multimedia_file_refn);


--
-- Name: media_file_m_file_multimedia_format_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX media_file_m_file_multimedia_format_index ON public.wt_media_file USING btree (m_file, multimedia_format);


--
-- Name: media_file_m_file_source_media_type_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX media_file_m_file_source_media_type_index ON public.wt_media_file USING btree (m_file, source_media_type);


--
-- Name: media_file_m_id_m_file_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX media_file_m_id_m_file_index ON public.wt_media_file USING btree (m_id, m_file);


--
-- Name: message_user_id_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX message_user_id_index ON public.wt_message USING btree (user_id);


--
-- Name: name_n_full_n_id_n_file_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX name_n_full_n_id_n_file_index ON public.wt_name USING btree (n_full, n_id, n_file);


--
-- Name: name_n_givn_n_file_n_type_n_id_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX name_n_givn_n_file_n_type_n_id_index ON public.wt_name USING btree (n_givn, n_file, n_type, n_id);


--
-- Name: name_n_surn_n_file_n_type_n_id_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX name_n_surn_n_file_n_type_n_id_index ON public.wt_name USING btree (n_surn, n_file, n_type, n_id);


--
-- Name: news_gedcom_id_updated_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX news_gedcom_id_updated_index ON public.wt_news USING btree (gedcom_id, updated);


--
-- Name: news_user_id_updated_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX news_user_id_updated_index ON public.wt_news USING btree (user_id, updated);


--
-- Name: place_location_latitude_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX place_location_latitude_index ON public.wt_place_location USING btree (latitude);


--
-- Name: place_location_longitude_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX place_location_longitude_index ON public.wt_place_location USING btree (longitude);


--
-- Name: placelinks_pl_file_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX placelinks_pl_file_index ON public.wt_placelinks USING btree (pl_file);


--
-- Name: placelinks_pl_gid_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX placelinks_pl_gid_index ON public.wt_placelinks USING btree (pl_gid);


--
-- Name: placelinks_pl_p_id_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX placelinks_pl_p_id_index ON public.wt_placelinks USING btree (pl_p_id);


--
-- Name: places_p_file_p_place_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX places_p_file_p_place_index ON public.wt_places USING btree (p_file, p_place);


--
-- Name: session_session_time_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX session_session_time_index ON public.wt_session USING btree (session_time);


--
-- Name: session_user_id_ip_address_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX session_user_id_ip_address_index ON public.wt_session USING btree (user_id, ip_address);


--
-- Name: sources_s_name_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX sources_s_name_index ON public.wt_sources USING btree (s_name);


--
-- Name: user_gedcom_setting_gedcom_id_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX user_gedcom_setting_gedcom_id_index ON public.wt_user_gedcom_setting USING btree (gedcom_id);


--
-- Name: wt_block block_gedcom_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.wt_block
    ADD CONSTRAINT block_gedcom_id_foreign FOREIGN KEY (gedcom_id) REFERENCES public.wt_gedcom(gedcom_id);


--
-- Name: wt_block block_module_name_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.wt_block
    ADD CONSTRAINT block_module_name_foreign FOREIGN KEY (module_name) REFERENCES public.wt_module(module_name);


--
-- Name: wt_block_setting block_setting_block_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.wt_block_setting
    ADD CONSTRAINT block_setting_block_id_foreign FOREIGN KEY (block_id) REFERENCES public.wt_block(block_id);


--
-- Name: wt_block block_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.wt_block
    ADD CONSTRAINT block_user_id_foreign FOREIGN KEY (user_id) REFERENCES public.wt_user(user_id);


--
-- Name: wt_change change_gedcom_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.wt_change
    ADD CONSTRAINT change_gedcom_id_foreign FOREIGN KEY (gedcom_id) REFERENCES public.wt_gedcom(gedcom_id);


--
-- Name: wt_change change_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.wt_change
    ADD CONSTRAINT change_user_id_foreign FOREIGN KEY (user_id) REFERENCES public.wt_user(user_id);


--
-- Name: wt_default_resn default_resn_gedcom_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.wt_default_resn
    ADD CONSTRAINT default_resn_gedcom_id_foreign FOREIGN KEY (gedcom_id) REFERENCES public.wt_gedcom(gedcom_id);


--
-- Name: wt_favorite favorite_gedcom_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.wt_favorite
    ADD CONSTRAINT favorite_gedcom_id_foreign FOREIGN KEY (gedcom_id) REFERENCES public.wt_gedcom(gedcom_id) ON DELETE CASCADE;


--
-- Name: wt_favorite favorite_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.wt_favorite
    ADD CONSTRAINT favorite_user_id_foreign FOREIGN KEY (user_id) REFERENCES public.wt_user(user_id) ON DELETE CASCADE;


--
-- Name: wt_gedcom_chunk gedcom_chunk_gedcom_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.wt_gedcom_chunk
    ADD CONSTRAINT gedcom_chunk_gedcom_id_foreign FOREIGN KEY (gedcom_id) REFERENCES public.wt_gedcom(gedcom_id);


--
-- Name: wt_gedcom_setting gedcom_setting_gedcom_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.wt_gedcom_setting
    ADD CONSTRAINT gedcom_setting_gedcom_id_foreign FOREIGN KEY (gedcom_id) REFERENCES public.wt_gedcom(gedcom_id);


--
-- Name: wt_hit_counter hit_counter_gedcom_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.wt_hit_counter
    ADD CONSTRAINT hit_counter_gedcom_id_foreign FOREIGN KEY (gedcom_id) REFERENCES public.wt_gedcom(gedcom_id);


--
-- Name: wt_log log_gedcom_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.wt_log
    ADD CONSTRAINT log_gedcom_id_foreign FOREIGN KEY (gedcom_id) REFERENCES public.wt_gedcom(gedcom_id);


--
-- Name: wt_log log_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.wt_log
    ADD CONSTRAINT log_user_id_foreign FOREIGN KEY (user_id) REFERENCES public.wt_user(user_id);


--
-- Name: wt_message message_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.wt_message
    ADD CONSTRAINT message_user_id_foreign FOREIGN KEY (user_id) REFERENCES public.wt_user(user_id);


--
-- Name: wt_module_privacy module_privacy_gedcom_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.wt_module_privacy
    ADD CONSTRAINT module_privacy_gedcom_id_foreign FOREIGN KEY (gedcom_id) REFERENCES public.wt_gedcom(gedcom_id) ON DELETE CASCADE;


--
-- Name: wt_module_privacy module_privacy_module_name_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.wt_module_privacy
    ADD CONSTRAINT module_privacy_module_name_foreign FOREIGN KEY (module_name) REFERENCES public.wt_module(module_name) ON DELETE CASCADE;


--
-- Name: wt_module_setting module_setting_module_name_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.wt_module_setting
    ADD CONSTRAINT module_setting_module_name_foreign FOREIGN KEY (module_name) REFERENCES public.wt_module(module_name);


--
-- Name: wt_news news_gedcom_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.wt_news
    ADD CONSTRAINT news_gedcom_id_foreign FOREIGN KEY (gedcom_id) REFERENCES public.wt_gedcom(gedcom_id) ON DELETE CASCADE;


--
-- Name: wt_news news_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.wt_news
    ADD CONSTRAINT news_user_id_foreign FOREIGN KEY (user_id) REFERENCES public.wt_user(user_id) ON DELETE CASCADE;


--
-- Name: wt_place_location place_location_parent_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.wt_place_location
    ADD CONSTRAINT place_location_parent_id_foreign FOREIGN KEY (parent_id) REFERENCES public.wt_place_location(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: wt_user_gedcom_setting user_gedcom_setting_gedcom_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.wt_user_gedcom_setting
    ADD CONSTRAINT user_gedcom_setting_gedcom_id_foreign FOREIGN KEY (gedcom_id) REFERENCES public.wt_gedcom(gedcom_id);


--
-- Name: wt_user_gedcom_setting user_gedcom_setting_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.wt_user_gedcom_setting
    ADD CONSTRAINT user_gedcom_setting_user_id_foreign FOREIGN KEY (user_id) REFERENCES public.wt_user(user_id);


--
-- Name: wt_user_setting user_setting_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.wt_user_setting
    ADD CONSTRAINT user_setting_user_id_foreign FOREIGN KEY (user_id) REFERENCES public.wt_user(user_id);


--
-- PostgreSQL database dump complete
--

\unrestrict V2Qe4tcY83q0eNhb2g1tuCcK8E9FcvWUiz2HZM1H29QmqLVW5BU7sKWhsvbhgnh
