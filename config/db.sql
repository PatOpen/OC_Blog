--
-- PostgreSQL database dump
--

-- Dumped from database version 12.4 (Ubuntu 12.4-0ubuntu0.20.04.1)
-- Dumped by pg_dump version 12.4 (Ubuntu 12.4-0ubuntu0.20.04.1)

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

--
-- Name: OC_Blog; Type: DATABASE; Schema: -; Owner: lefumier
--

CREATE DATABASE "OC_Blog" WITH TEMPLATE = template0 ENCODING = 'UTF8' LC_COLLATE = 'fr_FR.UTF-8' LC_CTYPE = 'fr_FR.UTF-8';


ALTER DATABASE "OC_Blog" OWNER TO lefumier;

\connect "OC_Blog"

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
-- Name: admin; Type: TABLE; Schema: public; Owner: lefumier
--

CREATE TABLE public.admin (
    id integer NOT NULL,
    user_id integer NOT NULL,
    create_at timestamp without time zone NOT NULL,
    role integer
);


ALTER TABLE public.admin OWNER TO lefumier;

--
-- Name: comment; Type: TABLE; Schema: public; Owner: lefumier
--

CREATE TABLE public.comment (
    id integer NOT NULL,
    user_id integer NOT NULL,
    post_id integer NOT NULL,
    create_at timestamp without time zone NOT NULL,
    content character varying NOT NULL,
    validation boolean DEFAULT false NOT NULL,
    modified_at timestamp without time zone
);


ALTER TABLE public.comment OWNER TO lefumier;

--
-- Name: comment_id_seq; Type: SEQUENCE; Schema: public; Owner: lefumier
--

CREATE SEQUENCE public.comment_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.comment_id_seq OWNER TO lefumier;

--
-- Name: comment_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: lefumier
--

ALTER SEQUENCE public.comment_id_seq OWNED BY public.comment.id;


--
-- Name: post; Type: TABLE; Schema: public; Owner: lefumier
--

CREATE TABLE public.post (
    id integer NOT NULL,
    title character varying(50) NOT NULL,
    chapo character varying(255) NOT NULL,
    description text NOT NULL,
    create_at timestamp without time zone NOT NULL,
    modified_at timestamp without time zone,
    user_id integer NOT NULL,
    image character varying(100) NOT NULL
);


ALTER TABLE public.post OWNER TO lefumier;

--
-- Name: post_id_seq; Type: SEQUENCE; Schema: public; Owner: lefumier
--

CREATE SEQUENCE public.post_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.post_id_seq OWNER TO lefumier;

--
-- Name: post_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: lefumier
--

ALTER SEQUENCE public.post_id_seq OWNED BY public.post.id;


--
-- Name: users; Type: TABLE; Schema: public; Owner: lefumier
--

CREATE TABLE public.users (
    id integer NOT NULL,
    pseudo character varying(20) NOT NULL,
    email character varying(100) NOT NULL,
    password character varying NOT NULL,
    create_at timestamp without time zone NOT NULL,
    avatar character varying,
    admin boolean DEFAULT false NOT NULL
);


ALTER TABLE public.users OWNER TO lefumier;

--
-- Name: users_id_seq; Type: SEQUENCE; Schema: public; Owner: lefumier
--

CREATE SEQUENCE public.users_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.users_id_seq OWNER TO lefumier;

--
-- Name: users_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: lefumier
--

ALTER SEQUENCE public.users_id_seq OWNED BY public.users.id;


--
-- Name: comment id; Type: DEFAULT; Schema: public; Owner: lefumier
--

ALTER TABLE ONLY public.comment ALTER COLUMN id SET DEFAULT nextval('public.comment_id_seq'::regclass);


--
-- Name: post id; Type: DEFAULT; Schema: public; Owner: lefumier
--

ALTER TABLE ONLY public.post ALTER COLUMN id SET DEFAULT nextval('public.post_id_seq'::regclass);


--
-- Name: users id; Type: DEFAULT; Schema: public; Owner: lefumier
--

ALTER TABLE ONLY public.users ALTER COLUMN id SET DEFAULT nextval('public.users_id_seq'::regclass);


--
-- Data for Name: admin; Type: TABLE DATA; Schema: public; Owner: lefumier
--

COPY public.admin (id, user_id, create_at, role) FROM stdin;
1	2	2020-08-21 11:12:10	3
2	7	2020-08-30 10:54:32	3
\.


--
-- Data for Name: comment; Type: TABLE DATA; Schema: public; Owner: lefumier
--

COPY public.comment (id, user_id, post_id, create_at, content, validation, modified_at) FROM stdin;
4	2	1	2020-08-20 19:45:24.465288	var_dump($coment);	t	2020-08-23 18:59:02.797316
15	2	2	2020-08-30 12:52:23.353049	Salut tout le monde je suis ravi de faire ce commentaire !	t	\N
17	2	13	2020-09-02 19:15:10.313323	Je suis en train de vérifier que tout se passe bien !	t	2020-09-02 19:16:42.708689
12	2	1	2020-08-26 17:38:47.095115	Salut je suis un autre commentaire et c'est tout	t	2020-08-31 23:53:11.113648
8	4	1	2020-08-23 19:37:20.671814	Tien je rajoute un commentaire	t	\N
13	2	1	2020-08-28 14:05:16.965162	Salut Jean i fait beau chez toi...	t	\N
7	2	1	2020-08-23 18:52:52.36036	Je suis un autre nouveau et c&#039;est tout	t	2020-08-28 14:06:09.093578
\.


--
-- Data for Name: post; Type: TABLE DATA; Schema: public; Owner: lefumier
--

COPY public.post (id, title, chapo, description, create_at, modified_at, user_id, image) FROM stdin;
4	Nouveau Postgres	Dolores harum alias consequatur blanditiis. Inventore, quod, ullam veritatis eum ratione neque quis fugit quae optio facilis in ipsa! Maiores, quia, possimus repellendus iusto nostrum nisi doloribu	<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Impedit, rerum voluptates veniam. Esse, nihil, ea, eaque, quos cum id tempore voluptate nisi nemo debitis impedit officiis culpa repellat voluptatum in aperiam error quo minima ratione ex pariatur maxime eligendi dolore nesciunt molestiae enim alias atque commodi delectus perferendis. Blanditiis, iste placeat nostrum in! Eligendi, omnis, unde, quos ullam nesciunt molestias quis a saepe nisi distinctio molestiae voluptate obcaecati officiis consequuntur similique aspernatur rerum sequi placeat iure quaerat itaque libero officia recusandae ad corrupti aperiam cum beatae. Adipisci ad natus deleniti.</p>\r\n<p>Dolores harum alias consequatur blanditiis. Inventore, quod, ullam veritatis eum ratione neque quis fugit quae optio facilis in ipsa! Maiores, quia, possimus repellendus iusto nostrum nisi doloribus qui excepturi ducimus veritatis molestiae autem consequatur quae ex nihil id sapiente minima adipisci. Quibusdam, aperiam, sapiente, nobis, possimus vero laudantium delectus esse minus quo nihil perspiciatis accusamus. Cupiditate sapiente illum accusantium animi pariatur sed minima nam. </p>	2020-08-31 13:43:09.634984	2020-08-31 23:52:17.386691	2	d4116df66f683c47ac861af834f8954b.png
2	Développer en PHP	Lorem ipsum dolor sit amet, consectetur adipisicing elit. Enim, itaque, porro, non sequi sunt vel quibusdam harum ea ratione ipsum eius eos maxime vitae hic ab dignissimos natus assumenda similique earum laborio	Dolores harum alias consequatur blanditiis. Inventore, quod, ullam veritatis eum ratione neque quis fugit quae optio facilis in ipsa! Maiores, quia, possimus repellendus iusto nostrum nisi doloribus qui excepturi ducimus veritatis molestiae autem consequatur quae ex nihil id sapiente minima adipisci. Quibusdam, aperiam, sapiente, nobis, possimus vero laudantium delectus esse minus quo nihil perspiciatis accusamus. Cupiditate sapiente illum accusantium animi pariatur sed minima nam. \r\n\r\nLorem ipsum dolor sit amet, consectetur adipisicing elit. Enim, itaque, porro, non sequi sunt vel quibusdam harum ea ratione ipsum eius eos maxime vitae hic ab dignissimos natus assumenda similique earum laboriosam possimus debitis molestiae. Commodi, facilis, et, officia hic quos quas ipsam harum amet illum quia reprehenderit sequi repellat\r\n\r\nLorem ipsum dolor sit amet, consectetur adipisicing elit. Enim, itaque, porro, non sequi sunt vel quibusdam harum ea ratione ipsum eius eos maxime vitae hic ab dignissimos natus assumenda similique earum laboriosam possimus debitis molestiae. Commodi, facilis, et, officia hic quos quas ipsam harum amet illum quia reprehenderit sequi repellat. Asperiores, fugiat, opt	2020-08-30 01:26:55.746085	2020-09-02 09:42:30.522467	2	61a8719e9936e882f6827ac336ff9e54.png
1	Le HTML et le CSS	Dolores harum alias consequatur blanditiis. Inventore, quod, ullam veritatis eum ratione neque quis fugit quae optio facilis in ipsa! Ma	Harum, consectetur, quia nisi fugiat quasi ea amet cum inventore aperiam optio qui perspiciatis debitis molestiae laborum minima doloremque ullam eum nesciunt repellendus dolor dicta cumque deserunt. Quaerat, saepe, maxime, quod, dolor nisi assumenda aut est quos optio animi aliquid quidem voluptates adipisci hic recusandae dicta quis eligendi illo ducimus asperiores reiciendis odit distinctio natus? In, reprehenderit, facere, ipsam, vel architecto autem temporibus a quidem voluptatum at nesciunt quas facilis. Soluta, excepturi, nihil, illum dicta hic ratione tenetur voluptate dolorum a tempore dignissimos reprehenderit voluptas expedita officiis enim minus adipisci?\r\n\r\nLorem ipsum dolor sit amet, consectetur adipisicing elit. Enim, itaque, porro, non sequi sunt vel quibusdam harum ea ratione ipsum eius eos maxime vitae hic ab dignissimos natus assumenda similique earum laboriosam possimus debitis molestiae. Commodi, facilis, et, officia hic quos quas ipsam harum amet illum quia reprehenderit sequi repellat. Asperiores, fugiat, optio reiciendis nam nisi assumenda alias non similique modi ducimus nostrum quasi consequatur ipsa animi soluta!	2020-08-11 11:53:18.750539	2020-08-31 16:46:57.958855	2	48a5ff2f5f601e97aee1e329c0de94c8.png
12	Angular , javascript	Dolores harum alias consequatur blanditiis. Inventore, quod, ullam veritatis eum ratione neque quis fugit quae optio facilis in ipsa! Maiores, quia, possimus repellendus iusto nostrum nisi doloribus qu	Lorem ipsum dolor sit amet, consectetur adipisicing elit. Enim, itaque, porro, non sequi sunt vel quibusdam harum ea ratione ipsum eius eos maxime vitae hic ab dignissimos natus assumenda similique earum laboriosam possimus debitis molestiae. Commodi, facilis, et, officia hic quos quas ipsam harum amet illum quia reprehenderit sequi repellat. Asperiores, fugiat, optio reiciendis nam nisi assumenda alias non similique modi ducimus nostrum quasi consequatur ipsa animi soluta!\r\n\r\n\r\nLorem ipsum dolor sit amet, consectetur adipisicing elit. Impedit, rerum voluptates veniam. Esse, nihil, ea, eaque, quos cum id tempore voluptate nisi nemo debitis impedit officiis culpa repellat voluptatum in aperiam error quo minima ratione ex pariatur maxime eligendi dolore nesciunt molestiae enim alias atque commodi delectus perferendis. Blanditiis, iste placeat nostrum in! Eligendi, omnis, unde, quos ullam nesciunt molestias quis a saepe nisi distinctio molestiae voluptate obcaecati officiis consequuntur similique aspernatur rerum sequi placeat iure quaerat itaque libero officia recusandae ad corrupti aperiam cum beatae. Adipisci ad natus deleniti.	2020-08-31 16:49:35.840872	2020-08-31 17:27:26.232726	2	8a99945dd811e59478e1af7df71a637a.png
3	Framework Symfony	Cetainement le meilleur framework PHP qui existe parmi toute les solutions  enim. Suspendisse at semper ipquis. Mauris commodo rhoncus porttit	Mauris eget quam orci. Quisque porta varius dui, quis posuere nibh mollis quis. Mauris commodo rhoncus porttitor. Maecenas et euismod elit. Nulla facilisi. Vivamus lacus libero, ultrices non ullamcorper ac, tempus sit amet enim. Suspendisse at semper ipsum. Suspendisse sagittis diam a massa viverra sollicitudin. Vivamus sagittis est eu diam fringilla nec tristique metus vestibulum. Donec magna purus, pellentesque vel lobortis ut, convallis id augue. Sed odio magna, pellentesque eget convallis ac, vehicula vel arcu. Sed eu scelerisque dui. Sed eu arcu at nibh hendrerit viverra. Vivamus lacus augue, sodales id cursus in, condimentum at risus.\r\n\r\nMauris eget quam orci. Quisque porta varius dui, quis posuere nibh mollis quis. Mauris commodo rhoncus porttitor. Maecenas et euismod elit. Nulla facilisi. Vivamus lacus libero, ultrices non ullamcorper ac, tempus sit amet enim. Suspendisse at semper ipsum. Suspendisse sagittis diam a massa viverra sollicitudin. Vivamus sagittis est eu diam fringilla nec tristique metus vestibulum. Donec magna purus, pellentesque vel lobortis ut, convallis id augue. Sed odio magna, pellentesque eget convallis ac, vehicula vel arcu. Sed eu scelerisque dui. Sed eu arcu at nibh hendrerit viverra. Vivamus lacus augue, sodales id cursus in, condimentum at risus.	2020-08-31 13:39:16.073104	2020-08-31 18:06:08.588734	2	c647562a36dd988190b9fb864cd005fe.png
13	Moteur Twig	I don't think they tried to market it to the billionaire, spelunking, base-jumping crowd. i did the   same thing to gandhi, he didn't eat for three weeks. i once heard a wise man say there are no perfect men	Harum, consectetur, quia nisi fugiat quasi ea amet cum inventore aperiam optio qui perspiciatis debitis molestiae laborum minima doloremque ullam eum nesciunt repellendus dolor dicta cumque deserunt. Quaerat, saepe, maxime, quod, dolor nisi assumenda aut est quos optio animi aliquid quidem voluptates adipisci hic recusandae dicta quis eligendi illo ducimus asperiores reiciendis odit distinctio natus? In, reprehenderit, facere, ipsam, vel architecto autem temporibus a quidem voluptatum at nesciunt quas facilis. Soluta, excepturi, nihil, illum dicta hic ratione tenetur voluptate dolorum a tempore dignissimos reprehenderit voluptas expedita<br>\r\n\r\nDolores harum alias consequatur blanditiis. Inventore, quod, ullam veritatis eum ratione neque quis fugit quae optio facilis in ipsa! Maiores, quia, possimus repellendus iusto nostrum nisi doloribus qui excepturi ducimus veritatis molestiae autem consequatur quae ex nihil id sapiente minima adipisci. Quibusdam, aperiam, sapiente, nobis, possimus vero laudantium delectus esse minus quo nihil perspiciatis accusamus. Cupiditate sapiente illum accusantium animi pariatur sed minima nam. 	2020-08-31 17:53:39.662484	2020-09-02 19:19:56.63154	2	f3e61b98d98b1b9a03d3b6ef9632d435.png
\.


--
-- Data for Name: users; Type: TABLE DATA; Schema: public; Owner: lefumier
--

COPY public.users (id, pseudo, email, password, create_at, avatar, admin) FROM stdin;
2	Patou	pat@live.fr	$2y$10$QFuBqOCZEb6nIcFWF6KWVuam0j3Ljc08M8egWEni.osIQIAkReBaC	2020-08-12 00:27:11.86862	13d46253a131ef6dab3965417a11364b.png	t
4	Bill	bill@live.fr	$2y$10$nvyQ3Tp84b98go1lvv5JO.l97bFKOc9MmWBLcqSV3muTdpNuEncSW	2020-08-23 19:35:15.659957	1f9b7a1c3ed581a351bd788a5ab29c6b.png	f
7	admin	admin@admin.fr	$2y$10$ePUUcifyru7Z/fuPlb0h1uwLgR2jgLYoegUZESIBtoDh6Lc5OvRWG	2020-09-03 17:25:35.483324	045456a40ae3971ad6007b12c03bc07c.png	t
\.


--
-- Name: comment_id_seq; Type: SEQUENCE SET; Schema: public; Owner: lefumier
--

SELECT pg_catalog.setval('public.comment_id_seq', 17, true);


--
-- Name: post_id_seq; Type: SEQUENCE SET; Schema: public; Owner: lefumier
--

SELECT pg_catalog.setval('public.post_id_seq', 17, true);


--
-- Name: users_id_seq; Type: SEQUENCE SET; Schema: public; Owner: lefumier
--

SELECT pg_catalog.setval('public.users_id_seq', 9, true);


--
-- Name: admin admin_pk; Type: CONSTRAINT; Schema: public; Owner: lefumier
--

ALTER TABLE ONLY public.admin
    ADD CONSTRAINT admin_pk PRIMARY KEY (id);


--
-- Name: comment comment_pk; Type: CONSTRAINT; Schema: public; Owner: lefumier
--

ALTER TABLE ONLY public.comment
    ADD CONSTRAINT comment_pk PRIMARY KEY (id);


--
-- Name: post post_pk; Type: CONSTRAINT; Schema: public; Owner: lefumier
--

ALTER TABLE ONLY public.post
    ADD CONSTRAINT post_pk PRIMARY KEY (id);


--
-- Name: users users_pk; Type: CONSTRAINT; Schema: public; Owner: lefumier
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_pk PRIMARY KEY (id);


--
-- Name: comment post_comment_fk; Type: FK CONSTRAINT; Schema: public; Owner: lefumier
--

ALTER TABLE ONLY public.comment
    ADD CONSTRAINT post_comment_fk FOREIGN KEY (post_id) REFERENCES public.post(id);


--
-- Name: admin user_admin_fk; Type: FK CONSTRAINT; Schema: public; Owner: lefumier
--

ALTER TABLE ONLY public.admin
    ADD CONSTRAINT user_admin_fk FOREIGN KEY (user_id) REFERENCES public.users(id);


--
-- Name: comment user_comment_fk; Type: FK CONSTRAINT; Schema: public; Owner: lefumier
--

ALTER TABLE ONLY public.comment
    ADD CONSTRAINT user_comment_fk FOREIGN KEY (user_id) REFERENCES public.users(id);


--
-- Name: post user_post_fk; Type: FK CONSTRAINT; Schema: public; Owner: lefumier
--

ALTER TABLE ONLY public.post
    ADD CONSTRAINT user_post_fk FOREIGN KEY (user_id) REFERENCES public.users(id);


--
-- PostgreSQL database dump complete
--

