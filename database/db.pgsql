--
-- PostgreSQL database dump
--

-- Dumped from database version 12.16 (Ubuntu 12.16-0ubuntu0.20.04.1)
-- Dumped by pg_dump version 12.16 (Ubuntu 12.16-0ubuntu0.20.04.1)

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
-- Name: agent_payment_logs; Type: TABLE; Schema: public; Owner: trekka
--

CREATE TABLE public.agent_payment_logs (
    uuid character varying(191) NOT NULL,
    agent_uuid character varying(191),
    customer_uuid character varying(191),
    type character varying(191) DEFAULT 'deposit'::character varying,
    ref character varying(191) DEFAULT 'payment-discount'::character varying,
    ref_uuid character varying(191),
    total double precision DEFAULT '0'::double precision,
    total_currency character varying(191) DEFAULT 'VND'::character varying,
    discount double precision DEFAULT '0'::double precision,
    amount double precision DEFAULT '0'::double precision,
    currency character varying(191) DEFAULT 'VND'::character varying,
    coefficient integer DEFAULT 1,
    is_reported integer DEFAULT 0,
    note text,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.agent_payment_logs OWNER TO trekka;

--
-- Name: auth_logs; Type: TABLE; Schema: public; Owner: trekka
--

CREATE TABLE public.auth_logs (
    uuid character varying(191) NOT NULL,
    user_uuid character varying(191) DEFAULT '0'::character varying NOT NULL,
    status integer DEFAULT 0 NOT NULL,
    log_fail_count integer DEFAULT 0 NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.auth_logs OWNER TO trekka;

--
-- Name: campaigns; Type: TABLE; Schema: public; Owner: trekka
--

CREATE TABLE public.campaigns (
    uuid character varying(191) NOT NULL,
    name character varying(191),
    description text,
    vendor_uuid character varying(191),
    voucher_type character varying(191) DEFAULT 'tour'::character varying,
    total_quantity integer DEFAULT 0,
    issuance_costs double precision DEFAULT '0'::double precision,
    original_price double precision DEFAULT '0'::double precision,
    discount double precision DEFAULT '0'::double precision,
    payment_amount double precision DEFAULT '0'::double precision,
    ref_url character varying(191),
    status character varying(191) DEFAULT 'running'::character varying,
    expire_days integer DEFAULT 0,
    display_in integer DEFAULT 0,
    customer_config json,
    started_at timestamp(0) without time zone,
    expired_at timestamp(0) without time zone,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.campaigns OWNER TO trekka;

--
-- Name: COLUMN campaigns.uuid; Type: COMMENT; Schema: public; Owner: trekka
--

COMMENT ON COLUMN public.campaigns.uuid IS 'Campaign UUID';


--
-- Name: COLUMN campaigns.name; Type: COMMENT; Schema: public; Owner: trekka
--

COMMENT ON COLUMN public.campaigns.name IS 'Tên chiến dịch';


--
-- Name: COLUMN campaigns.description; Type: COMMENT; Schema: public; Owner: trekka
--

COMMENT ON COLUMN public.campaigns.description IS 'Mô tả';


--
-- Name: COLUMN campaigns.vendor_uuid; Type: COMMENT; Schema: public; Owner: trekka
--

COMMENT ON COLUMN public.campaigns.vendor_uuid IS 'ID Nhà cung cấp';


--
-- Name: COLUMN campaigns.voucher_type; Type: COMMENT; Schema: public; Owner: trekka
--

COMMENT ON COLUMN public.campaigns.voucher_type IS 'Loại voucher';


--
-- Name: COLUMN campaigns.total_quantity; Type: COMMENT; Schema: public; Owner: trekka
--

COMMENT ON COLUMN public.campaigns.total_quantity IS 'Tổng số voucher phát hành';


--
-- Name: COLUMN campaigns.issuance_costs; Type: COMMENT; Schema: public; Owner: trekka
--

COMMENT ON COLUMN public.campaigns.issuance_costs IS 'Chi phí phát hành';


--
-- Name: COLUMN campaigns.original_price; Type: COMMENT; Schema: public; Owner: trekka
--

COMMENT ON COLUMN public.campaigns.original_price IS 'Giá gốc';


--
-- Name: COLUMN campaigns.discount; Type: COMMENT; Schema: public; Owner: trekka
--

COMMENT ON COLUMN public.campaigns.discount IS 'Chiết khấu';


--
-- Name: COLUMN campaigns.payment_amount; Type: COMMENT; Schema: public; Owner: trekka
--

COMMENT ON COLUMN public.campaigns.payment_amount IS 'Số tiền thanh toán';


--
-- Name: COLUMN campaigns.ref_url; Type: COMMENT; Schema: public; Owner: trekka
--

COMMENT ON COLUMN public.campaigns.ref_url IS 'URL Chiến dịch';


--
-- Name: COLUMN campaigns.status; Type: COMMENT; Schema: public; Owner: trekka
--

COMMENT ON COLUMN public.campaigns.status IS 'Trạng thái';


--
-- Name: COLUMN campaigns.expire_days; Type: COMMENT; Schema: public; Owner: trekka
--

COMMENT ON COLUMN public.campaigns.expire_days IS 'Ngày hết hạn';


--
-- Name: COLUMN campaigns.display_in; Type: COMMENT; Schema: public; Owner: trekka
--

COMMENT ON COLUMN public.campaigns.display_in IS 'Thời gian hiển thị';


--
-- Name: COLUMN campaigns.customer_config; Type: COMMENT; Schema: public; Owner: trekka
--

COMMENT ON COLUMN public.campaigns.customer_config IS 'Cấu hình khách hàng';


--
-- Name: COLUMN campaigns.started_at; Type: COMMENT; Schema: public; Owner: trekka
--

COMMENT ON COLUMN public.campaigns.started_at IS 'Thời gian bắt đầu chiến dịch';


--
-- Name: COLUMN campaigns.expired_at; Type: COMMENT; Schema: public; Owner: trekka
--

COMMENT ON COLUMN public.campaigns.expired_at IS 'Thời gian kết thúc chiến dịch';


--
-- Name: connect_packages; Type: TABLE; Schema: public; Owner: trekka
--

CREATE TABLE public.connect_packages (
    uuid character varying(191) NOT NULL,
    name character varying(191),
    description text,
    connect_count integer DEFAULT 0,
    price double precision DEFAULT '0'::double precision,
    currency character varying(191) DEFAULT 'VND'::character varying,
    discount double precision DEFAULT '0'::double precision,
    is_default boolean DEFAULT false,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.connect_packages OWNER TO trekka;

--
-- Name: connect_requires; Type: TABLE; Schema: public; Owner: trekka
--

CREATE TABLE public.connect_requires (
    uuid character varying(191) NOT NULL,
    user_uuid character varying(191),
    place_uuid character varying(191),
    title character varying(191),
    description text,
    from_date timestamp(0) without time zone,
    to_date timestamp(0) without time zone,
    expired_date timestamp(0) without time zone,
    budget double precision DEFAULT '0'::double precision,
    status character varying(191) DEFAULT 'OPENING'::character varying,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.connect_requires OWNER TO trekka;

--
-- Name: connected_partners; Type: TABLE; Schema: public; Owner: trekka
--

CREATE TABLE public.connected_partners (
    uuid character varying(191) NOT NULL,
    user_uuid character varying(191),
    partner_uuid character varying(191),
    conversation_uuid character varying(191),
    is_activated boolean DEFAULT false,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.connected_partners OWNER TO trekka;

--
-- Name: conversation_members; Type: TABLE; Schema: public; Owner: trekka
--

CREATE TABLE public.conversation_members (
    uuid character varying(191) NOT NULL,
    conversation_uuid character varying(191),
    member_uuid character varying(191),
    is_owner boolean DEFAULT false,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.conversation_members OWNER TO trekka;

--
-- Name: conversations; Type: TABLE; Schema: public; Owner: trekka
--

CREATE TABLE public.conversations (
    uuid character varying(191) NOT NULL,
    owner_uuid character varying(191),
    name character varying(191),
    type character varying(191) DEFAULT 'two-people'::character varying,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.conversations OWNER TO trekka;

--
-- Name: countries; Type: TABLE; Schema: public; Owner: trekka
--

CREATE TABLE public.countries (
    uuid character varying(191) NOT NULL,
    name character varying(191),
    slug character varying(191),
    code character varying(191),
    local character varying(191),
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    priority integer DEFAULT 0 NOT NULL
);


ALTER TABLE public.countries OWNER TO trekka;

--
-- Name: districts; Type: TABLE; Schema: public; Owner: trekka
--

CREATE TABLE public.districts (
    uuid character varying(191) NOT NULL,
    region_uuid character varying(191),
    code character varying(191),
    name character varying(191) NOT NULL,
    slug character varying(191) NOT NULL,
    type character varying(191),
    path_with_type character varying(191),
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.districts OWNER TO trekka;

--
-- Name: email_tokens; Type: TABLE; Schema: public; Owner: trekka
--

CREATE TABLE public.email_tokens (
    uuid character varying(191) NOT NULL,
    email character varying(191) NOT NULL,
    type character varying(191) DEFAULT 'confirm'::character varying NOT NULL,
    ref character varying(191),
    ref_uuid character varying(191) DEFAULT '0'::character varying,
    token character varying(191) NOT NULL,
    code character varying(6),
    expired_at timestamp(0) without time zone NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.email_tokens OWNER TO trekka;

--
-- Name: failed_jobs; Type: TABLE; Schema: public; Owner: trekka
--

CREATE TABLE public.failed_jobs (
    id bigint NOT NULL,
    uuid character varying(191) NOT NULL,
    connection text NOT NULL,
    queue text NOT NULL,
    payload text NOT NULL,
    exception text NOT NULL,
    failed_at timestamp(0) without time zone DEFAULT CURRENT_TIMESTAMP NOT NULL
);


ALTER TABLE public.failed_jobs OWNER TO trekka;

--
-- Name: failed_jobs_id_seq; Type: SEQUENCE; Schema: public; Owner: trekka
--

CREATE SEQUENCE public.failed_jobs_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.failed_jobs_id_seq OWNER TO trekka;

--
-- Name: failed_jobs_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: trekka
--

ALTER SEQUENCE public.failed_jobs_id_seq OWNED BY public.failed_jobs.id;


--
-- Name: file_refs; Type: TABLE; Schema: public; Owner: trekka
--

CREATE TABLE public.file_refs (
    uuid character varying(191) NOT NULL,
    file_uuid character varying(191) DEFAULT '0'::character varying NOT NULL,
    ref_uuid character varying(191) DEFAULT '0'::character varying NOT NULL,
    ref character varying(191),
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.file_refs OWNER TO trekka;

--
-- Name: files; Type: TABLE; Schema: public; Owner: trekka
--

CREATE TABLE public.files (
    uuid character varying(191) NOT NULL,
    upload_by character varying(191) DEFAULT '0'::character varying NOT NULL,
    privacy character varying(191) DEFAULT 'published'::character varying NOT NULL,
    ref character varying(191),
    ref_uuid character varying(191) DEFAULT '0'::character varying NOT NULL,
    folder_uuid character varying(191) DEFAULT '0'::character varying,
    date_path character varying(191),
    driver character varying(191) DEFAULT 'disk'::character varying,
    filename character varying(191),
    original_filename character varying(191),
    filetype character varying(191),
    mime character varying(191),
    size double precision DEFAULT '0'::double precision,
    extension character varying(191),
    title character varying(191),
    description text,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.files OWNER TO trekka;

--
-- Name: hobbies; Type: TABLE; Schema: public; Owner: trekka
--

CREATE TABLE public.hobbies (
    uuid character varying(191) NOT NULL,
    name character varying(191),
    keywords character varying(191),
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.hobbies OWNER TO trekka;

--
-- Name: mbti_details; Type: TABLE; Schema: public; Owner: trekka
--

CREATE TABLE public.mbti_details (
    uuid character varying(191) NOT NULL,
    mbti character varying(191),
    description text,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.mbti_details OWNER TO trekka;

--
-- Name: mbti_matches; Type: TABLE; Schema: public; Owner: trekka
--

CREATE TABLE public.mbti_matches (
    uuid character varying(191) NOT NULL,
    first_mbti character varying(191) NOT NULL,
    second_mbti character varying(191) NOT NULL,
    score integer DEFAULT 0 NOT NULL,
    description text,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.mbti_matches OWNER TO trekka;

--
-- Name: metadatas; Type: TABLE; Schema: public; Owner: trekka
--

CREATE TABLE public.metadatas (
    uuid character varying(191) NOT NULL,
    ref character varying(191) DEFAULT 'data'::character varying,
    ref_uuid character varying(191) DEFAULT '0'::character varying,
    name character varying(191) DEFAULT 'name'::character varying,
    value text,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.metadatas OWNER TO trekka;

--
-- Name: migrations; Type: TABLE; Schema: public; Owner: trekka
--

CREATE TABLE public.migrations (
    id integer NOT NULL,
    migration character varying(191) NOT NULL,
    batch integer NOT NULL
);


ALTER TABLE public.migrations OWNER TO trekka;

--
-- Name: migrations_id_seq; Type: SEQUENCE; Schema: public; Owner: trekka
--

CREATE SEQUENCE public.migrations_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.migrations_id_seq OWNER TO trekka;

--
-- Name: migrations_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: trekka
--

ALTER SEQUENCE public.migrations_id_seq OWNED BY public.migrations.id;


--
-- Name: notices; Type: TABLE; Schema: public; Owner: trekka
--

CREATE TABLE public.notices (
    uuid character varying(191) NOT NULL,
    created_by character varying(191) DEFAULT '0'::character varying,
    to_uuid character varying(191) DEFAULT '0'::character varying,
    to_group character varying(191),
    type character varying(191) DEFAULT 'personal'::character varying,
    title character varying(191) DEFAULT 'Bạn có thông báo mới'::character varying,
    message text,
    ref character varying(191),
    ref_uuid character varying(191) DEFAULT '0'::character varying,
    seen integer DEFAULT 0 NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.notices OWNER TO trekka;

--
-- Name: oauth_access_tokens; Type: TABLE; Schema: public; Owner: trekka
--

CREATE TABLE public.oauth_access_tokens (
    id character varying(100) NOT NULL,
    user_id character varying(191),
    client_id uuid NOT NULL,
    name character varying(191),
    scopes text,
    revoked boolean NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    expires_at timestamp(0) without time zone
);


ALTER TABLE public.oauth_access_tokens OWNER TO trekka;

--
-- Name: oauth_auth_codes; Type: TABLE; Schema: public; Owner: trekka
--

CREATE TABLE public.oauth_auth_codes (
    id character varying(100) NOT NULL,
    user_id character varying(191) NOT NULL,
    client_id uuid NOT NULL,
    scopes text,
    revoked boolean NOT NULL,
    expires_at timestamp(0) without time zone
);


ALTER TABLE public.oauth_auth_codes OWNER TO trekka;

--
-- Name: oauth_clients; Type: TABLE; Schema: public; Owner: trekka
--

CREATE TABLE public.oauth_clients (
    id uuid NOT NULL,
    user_id character varying(191),
    name character varying(191) NOT NULL,
    secret character varying(100),
    provider character varying(191),
    redirect text NOT NULL,
    personal_access_client boolean NOT NULL,
    password_client boolean NOT NULL,
    revoked boolean NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.oauth_clients OWNER TO trekka;

--
-- Name: oauth_personal_access_clients; Type: TABLE; Schema: public; Owner: trekka
--

CREATE TABLE public.oauth_personal_access_clients (
    id bigint NOT NULL,
    client_id uuid NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.oauth_personal_access_clients OWNER TO trekka;

--
-- Name: oauth_personal_access_clients_id_seq; Type: SEQUENCE; Schema: public; Owner: trekka
--

CREATE SEQUENCE public.oauth_personal_access_clients_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.oauth_personal_access_clients_id_seq OWNER TO trekka;

--
-- Name: oauth_personal_access_clients_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: trekka
--

ALTER SEQUENCE public.oauth_personal_access_clients_id_seq OWNED BY public.oauth_personal_access_clients.id;


--
-- Name: oauth_refresh_tokens; Type: TABLE; Schema: public; Owner: trekka
--

CREATE TABLE public.oauth_refresh_tokens (
    id character varying(100) NOT NULL,
    access_token_id character varying(100) NOT NULL,
    revoked boolean NOT NULL,
    expires_at timestamp(0) without time zone
);


ALTER TABLE public.oauth_refresh_tokens OWNER TO trekka;

--
-- Name: option_datas; Type: TABLE; Schema: public; Owner: trekka
--

CREATE TABLE public.option_datas (
    uuid character varying(191) NOT NULL,
    group_uuid character varying(191) NOT NULL,
    name character varying(191) DEFAULT 'option_name'::character varying NOT NULL,
    label character varying(191) DEFAULT 'Option Name'::character varying NOT NULL,
    type character varying(191) DEFAULT 'text'::character varying NOT NULL,
    value_type character varying(191) DEFAULT 'text'::character varying NOT NULL,
    value text,
    priority integer DEFAULT 12,
    props json,
    can_delete boolean DEFAULT false,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.option_datas OWNER TO trekka;

--
-- Name: option_groups; Type: TABLE; Schema: public; Owner: trekka
--

CREATE TABLE public.option_groups (
    uuid character varying(191) NOT NULL,
    option_uuid character varying(191) NOT NULL,
    label character varying(191) DEFAULT 'settings'::character varying,
    slug character varying(191) DEFAULT 'settings'::character varying,
    config text,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.option_groups OWNER TO trekka;

--
-- Name: options; Type: TABLE; Schema: public; Owner: trekka
--

CREATE TABLE public.options (
    uuid character varying(191) NOT NULL,
    title character varying(191) DEFAULT 'Options'::character varying,
    slug character varying(191) DEFAULT 'option'::character varying,
    ref character varying(191) DEFAULT 'base'::character varying,
    ref_uuid character varying(191) DEFAULT '0'::character varying,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.options OWNER TO trekka;

--
-- Name: partner_reports; Type: TABLE; Schema: public; Owner: trekka
--

CREATE TABLE public.partner_reports (
    uuid character varying(191) NOT NULL,
    reporter_uuid character varying(191),
    partner_uuid character varying(191),
    point integer DEFAULT 0 NOT NULL,
    message text,
    status integer DEFAULT 0,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.partner_reports OWNER TO trekka;

--
-- Name: partner_reviews; Type: TABLE; Schema: public; Owner: trekka
--

CREATE TABLE public.partner_reviews (
    uuid character varying(191) NOT NULL,
    reviewer_uuid character varying(191),
    partner_uuid character varying(191),
    point integer DEFAULT 0 NOT NULL,
    message text,
    is_approved boolean DEFAULT true,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.partner_reviews OWNER TO trekka;

--
-- Name: password_resets; Type: TABLE; Schema: public; Owner: trekka
--

CREATE TABLE public.password_resets (
    email character varying(191) NOT NULL,
    token character varying(191) NOT NULL,
    created_at timestamp(0) without time zone
);


ALTER TABLE public.password_resets OWNER TO trekka;

--
-- Name: payment_methods; Type: TABLE; Schema: public; Owner: trekka
--

CREATE TABLE public.payment_methods (
    uuid character varying(191) NOT NULL,
    name character varying(191),
    method character varying(191),
    description text,
    guide text,
    config text,
    priority integer DEFAULT 0 NOT NULL,
    status integer DEFAULT 1 NOT NULL,
    trashed_status integer DEFAULT 0 NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.payment_methods OWNER TO trekka;

--
-- Name: payment_requests; Type: TABLE; Schema: public; Owner: trekka
--

CREATE TABLE public.payment_requests (
    uuid character varying(191) NOT NULL,
    user_uuid character varying(191),
    transaction_uuid character varying(191),
    transaction_code character varying(191),
    order_uuid character varying(191) DEFAULT '0'::character varying,
    order_code character varying(191),
    amount double precision DEFAULT '0'::double precision,
    currency character varying(191) DEFAULT 'VND'::character varying,
    promotion_code character varying(191),
    note text,
    payment_method_name character varying(191),
    payment_method_uuid character varying(191) DEFAULT '0'::character varying,
    bank_code character varying(191),
    status integer DEFAULT 0,
    finished_at timestamp(0) without time zone,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    message text
);


ALTER TABLE public.payment_requests OWNER TO trekka;

--
-- Name: payment_transactions; Type: TABLE; Schema: public; Owner: trekka
--

CREATE TABLE public.payment_transactions (
    uuid character varying(191) NOT NULL,
    user_uuid character varying(191),
    method character varying(191),
    note character varying(191),
    description text,
    type smallint,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    transaction_uuid character varying(191),
    transaction_code character varying(191),
    order_uuid character varying(191) DEFAULT '0'::character varying,
    order_code character varying(191),
    amount double precision DEFAULT '0'::double precision,
    currency character varying(191) DEFAULT 'VND'::character varying,
    payment_method_name character varying(191),
    payment_method_uuid character varying(191) DEFAULT '0'::character varying,
    current_connect_count integer DEFAULT 0,
    package_connect_count integer DEFAULT 0,
    ref_code character varying(191),
    is_reported boolean DEFAULT false
);


ALTER TABLE public.payment_transactions OWNER TO trekka;

--
-- Name: COLUMN payment_transactions.type; Type: COMMENT; Schema: public; Owner: trekka
--

COMMENT ON COLUMN public.payment_transactions.type IS '1 là cộng tiền, 2 là trừ tiền';


--
-- Name: permission_module_group_actions; Type: TABLE; Schema: public; Owner: trekka
--

CREATE TABLE public.permission_module_group_actions (
    uuid character varying(191) NOT NULL,
    group_uuid character varying(191) DEFAULT '0'::character varying,
    action_uuid character varying(191) DEFAULT '0'::character varying,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.permission_module_group_actions OWNER TO trekka;

--
-- Name: permission_module_roles; Type: TABLE; Schema: public; Owner: trekka
--

CREATE TABLE public.permission_module_roles (
    uuid character varying(191) NOT NULL,
    module_uuid character varying(191),
    role_uuid character varying(191),
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.permission_module_roles OWNER TO trekka;

--
-- Name: permission_modules; Type: TABLE; Schema: public; Owner: trekka
--

CREATE TABLE public.permission_modules (
    uuid character varying(191) NOT NULL,
    type character varying(191) DEFAULT 'default'::character varying,
    name character varying(191),
    slug character varying(191),
    route character varying(191),
    prefix character varying(191),
    path character varying(191),
    parent_uuid character varying(191),
    ref character varying(191),
    description character varying(191),
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.permission_modules OWNER TO trekka;

--
-- Name: permission_roles; Type: TABLE; Schema: public; Owner: trekka
--

CREATE TABLE public.permission_roles (
    uuid character varying(191) NOT NULL,
    name character varying(191) NOT NULL,
    level integer DEFAULT 1 NOT NULL,
    description character varying(191),
    handle character varying(191),
    return_type character varying(191) DEFAULT 'redirect'::character varying,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.permission_roles OWNER TO trekka;

--
-- Name: permission_user_roles; Type: TABLE; Schema: public; Owner: trekka
--

CREATE TABLE public.permission_user_roles (
    uuid character varying(191) NOT NULL,
    user_uuid character varying(191),
    role_uuid character varying(191),
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.permission_user_roles OWNER TO trekka;

--
-- Name: personal_access_tokens; Type: TABLE; Schema: public; Owner: trekka
--

CREATE TABLE public.personal_access_tokens (
    id bigint NOT NULL,
    tokenable_type character varying(191) NOT NULL,
    tokenable_id bigint NOT NULL,
    name character varying(191) NOT NULL,
    token character varying(64) NOT NULL,
    abilities text,
    last_used_at timestamp(0) without time zone,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.personal_access_tokens OWNER TO trekka;

--
-- Name: personal_access_tokens_id_seq; Type: SEQUENCE; Schema: public; Owner: trekka
--

CREATE SEQUENCE public.personal_access_tokens_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.personal_access_tokens_id_seq OWNER TO trekka;

--
-- Name: personal_access_tokens_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: trekka
--

ALTER SEQUENCE public.personal_access_tokens_id_seq OWNED BY public.personal_access_tokens.id;


--
-- Name: places; Type: TABLE; Schema: public; Owner: trekka
--

CREATE TABLE public.places (
    uuid character varying(191) NOT NULL,
    region_uuid character varying(191),
    name character varying(191),
    slug character varying(191),
    keywords text,
    priority integer DEFAULT 0,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.places OWNER TO trekka;

--
-- Name: posts; Type: TABLE; Schema: public; Owner: trekka
--

CREATE TABLE public.posts (
    uuid character varying(191) NOT NULL,
    author_uuid character varying(191),
    dynamic_uuid character varying(191),
    parent_uuid character varying(191),
    category_uuid character varying(191),
    category_map character varying(191),
    type character varying(191) DEFAULT 'post'::character varying NOT NULL,
    content_type character varying(191) DEFAULT 'text'::character varying NOT NULL,
    title character varying(191) NOT NULL,
    slug character varying(191) NOT NULL,
    keywords character varying(191),
    description text,
    content text,
    featured_image character varying(191),
    views integer DEFAULT 0 NOT NULL,
    privacy character varying(191) DEFAULT 'published'::character varying NOT NULL,
    trashed_status integer DEFAULT 0 NOT NULL,
    deleted_at timestamp(0) without time zone,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.posts OWNER TO trekka;

--
-- Name: regions; Type: TABLE; Schema: public; Owner: trekka
--

CREATE TABLE public.regions (
    uuid character varying(191) NOT NULL,
    country_uuid character varying(191),
    name character varying(191) NOT NULL,
    slug character varying(191) NOT NULL,
    code character varying(191),
    "position" character varying(191),
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    keywords text,
    priority integer DEFAULT 0 NOT NULL
);


ALTER TABLE public.regions OWNER TO trekka;

--
-- Name: report_logs; Type: TABLE; Schema: public; Owner: trekka
--

CREATE TABLE public.report_logs (
    uuid character varying(191) NOT NULL,
    success integer DEFAULT 0 NOT NULL,
    fail integer DEFAULT 0 NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.report_logs OWNER TO trekka;

--
-- Name: require_partner_hobbies; Type: TABLE; Schema: public; Owner: trekka
--

CREATE TABLE public.require_partner_hobbies (
    uuid character varying(191) NOT NULL,
    user_uuid character varying(191),
    require_uuid character varying(191),
    hobby_uuid character varying(191),
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.require_partner_hobbies OWNER TO trekka;

--
-- Name: tag_refs; Type: TABLE; Schema: public; Owner: trekka
--

CREATE TABLE public.tag_refs (
    uuid character varying(191) NOT NULL,
    tag_uuid character varying(191) NOT NULL,
    ref character varying(191) DEFAULT 'post'::character varying,
    ref_uuid character varying(191) NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.tag_refs OWNER TO trekka;

--
-- Name: tags; Type: TABLE; Schema: public; Owner: trekka
--

CREATE TABLE public.tags (
    uuid character varying(191) NOT NULL,
    name character varying(191),
    name_lower character varying(191),
    keyword character varying(191),
    slug character varying(191) DEFAULT 'undefined'::character varying,
    tagged_count integer DEFAULT 0,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.tags OWNER TO trekka;

--
-- Name: trip_points; Type: TABLE; Schema: public; Owner: trekka
--

CREATE TABLE public.trip_points (
    uuid character varying(191) NOT NULL,
    user_uuid character varying(191),
    require_uuid character varying(191),
    place_uuid character varying(191),
    type character varying(191),
    title character varying(191),
    description text,
    from_date timestamp(0) without time zone,
    to_date timestamp(0) without time zone,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.trip_points OWNER TO trekka;

--
-- Name: user_hobbies; Type: TABLE; Schema: public; Owner: trekka
--

CREATE TABLE public.user_hobbies (
    uuid character varying(191) NOT NULL,
    user_uuid character varying(191),
    hobby_uuid character varying(191),
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.user_hobbies OWNER TO trekka;

--
-- Name: user_notices; Type: TABLE; Schema: public; Owner: trekka
--

CREATE TABLE public.user_notices (
    uuid character varying(191) NOT NULL,
    user_uuid character varying(191) DEFAULT '0'::character varying,
    notice_uuid character varying(191) DEFAULT '0'::character varying,
    seen integer DEFAULT 0 NOT NULL,
    seen_at timestamp(0) without time zone,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.user_notices OWNER TO trekka;

--
-- Name: users; Type: TABLE; Schema: public; Owner: trekka
--

CREATE TABLE public.users (
    uuid character varying(191) NOT NULL,
    full_name character varying(191) NOT NULL,
    gender character varying(191) DEFAULT 'MALE'::character varying,
    birthday date,
    email character varying(191) NOT NULL,
    username character varying(191) NOT NULL,
    password character varying(191) NOT NULL,
    phone character varying(191),
    avatar character varying(191),
    type character varying(191) DEFAULT 'user'::character varying,
    affiliate_code character varying(191),
    ref_code character varying(191),
    wallet_balance double precision DEFAULT '0'::double precision NOT NULL,
    agent_discount double precision DEFAULT '0'::double precision NOT NULL,
    connect_count integer DEFAULT 0 NOT NULL,
    country_code character varying(191) DEFAULT 'VN'::character varying,
    locale character varying(191) DEFAULT 'vi'::character varying,
    mbti character varying(191),
    trust_score integer DEFAULT 0,
    bio text,
    region_uuid character varying(191),
    district_uuid character varying(191),
    ward_uuid character varying(191),
    address character varying(191),
    identity_card_id character varying(191),
    is_verified_phone boolean DEFAULT false,
    is_verified_email boolean DEFAULT false,
    is_verified_identity boolean DEFAULT false,
    status integer DEFAULT 1 NOT NULL,
    google2fa_secret text,
    email_verified_at timestamp(0) without time zone,
    remember_token character varying(100),
    trashed_status integer DEFAULT 0 NOT NULL,
    deleted_at timestamp(0) without time zone,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    agent_expired_at date
);


ALTER TABLE public.users OWNER TO trekka;

--
-- Name: vouchers; Type: TABLE; Schema: public; Owner: trekka
--

CREATE TABLE public.vouchers (
    uuid character varying(191) NOT NULL,
    campaign_uuid character varying(191),
    user_uuid character varying(191),
    claim_user_uuid character varying(191),
    type character varying(191),
    code character varying(191),
    ref_url character varying(191),
    payment_url character varying(191),
    status character varying(191) DEFAULT 'idle'::character varying,
    is_claim integer DEFAULT 0,
    claim_expired_at timestamp(0) without time zone,
    expired_at timestamp(0) without time zone,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.vouchers OWNER TO trekka;

--
-- Name: COLUMN vouchers.uuid; Type: COMMENT; Schema: public; Owner: trekka
--

COMMENT ON COLUMN public.vouchers.uuid IS 'Voucher UUID';


--
-- Name: COLUMN vouchers.campaign_uuid; Type: COMMENT; Schema: public; Owner: trekka
--

COMMENT ON COLUMN public.vouchers.campaign_uuid IS 'ID của Chiến dịch';


--
-- Name: COLUMN vouchers.user_uuid; Type: COMMENT; Schema: public; Owner: trekka
--

COMMENT ON COLUMN public.vouchers.user_uuid IS 'ID của user';


--
-- Name: COLUMN vouchers.claim_user_uuid; Type: COMMENT; Schema: public; Owner: trekka
--

COMMENT ON COLUMN public.vouchers.claim_user_uuid IS 'ID của user claim Voucher';


--
-- Name: COLUMN vouchers.type; Type: COMMENT; Schema: public; Owner: trekka
--

COMMENT ON COLUMN public.vouchers.type IS 'Loại voucher';


--
-- Name: COLUMN vouchers.code; Type: COMMENT; Schema: public; Owner: trekka
--

COMMENT ON COLUMN public.vouchers.code IS 'Mã voucher';


--
-- Name: COLUMN vouchers.ref_url; Type: COMMENT; Schema: public; Owner: trekka
--

COMMENT ON COLUMN public.vouchers.ref_url IS 'URL Quảng cáo';


--
-- Name: COLUMN vouchers.payment_url; Type: COMMENT; Schema: public; Owner: trekka
--

COMMENT ON COLUMN public.vouchers.payment_url IS 'URL Thanh toán';


--
-- Name: COLUMN vouchers.status; Type: COMMENT; Schema: public; Owner: trekka
--

COMMENT ON COLUMN public.vouchers.status IS 'Trạng thái';


--
-- Name: COLUMN vouchers.is_claim; Type: COMMENT; Schema: public; Owner: trekka
--

COMMENT ON COLUMN public.vouchers.is_claim IS 'Trạng thái giữ';


--
-- Name: COLUMN vouchers.claim_expired_at; Type: COMMENT; Schema: public; Owner: trekka
--

COMMENT ON COLUMN public.vouchers.claim_expired_at IS 'Thời gian hết hạn nhận voucher';


--
-- Name: COLUMN vouchers.expired_at; Type: COMMENT; Schema: public; Owner: trekka
--

COMMENT ON COLUMN public.vouchers.expired_at IS 'Thời gian hết hạn voucher';


--
-- Name: wards; Type: TABLE; Schema: public; Owner: trekka
--

CREATE TABLE public.wards (
    uuid character varying(191) NOT NULL,
    district_uuid character varying(191) DEFAULT '0'::character varying NOT NULL,
    name character varying(191) NOT NULL,
    slug character varying(191) NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.wards OWNER TO trekka;

--
-- Name: failed_jobs id; Type: DEFAULT; Schema: public; Owner: trekka
--

ALTER TABLE ONLY public.failed_jobs ALTER COLUMN id SET DEFAULT nextval('public.failed_jobs_id_seq'::regclass);


--
-- Name: migrations id; Type: DEFAULT; Schema: public; Owner: trekka
--

ALTER TABLE ONLY public.migrations ALTER COLUMN id SET DEFAULT nextval('public.migrations_id_seq'::regclass);


--
-- Name: oauth_personal_access_clients id; Type: DEFAULT; Schema: public; Owner: trekka
--

ALTER TABLE ONLY public.oauth_personal_access_clients ALTER COLUMN id SET DEFAULT nextval('public.oauth_personal_access_clients_id_seq'::regclass);


--
-- Name: personal_access_tokens id; Type: DEFAULT; Schema: public; Owner: trekka
--

ALTER TABLE ONLY public.personal_access_tokens ALTER COLUMN id SET DEFAULT nextval('public.personal_access_tokens_id_seq'::regclass);


--
-- Data for Name: agent_payment_logs; Type: TABLE DATA; Schema: public; Owner: trekka
--

COPY public.agent_payment_logs (uuid, agent_uuid, customer_uuid, type, ref, ref_uuid, total, total_currency, discount, amount, currency, coefficient, is_reported, note, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: auth_logs; Type: TABLE DATA; Schema: public; Owner: trekka
--

COPY public.auth_logs (uuid, user_uuid, status, log_fail_count, created_at, updated_at) FROM stdin;
c1df4865-5443-4492-9f26-8fb59a8a9e9c	5ab97801-7ce3-477d-92f9-720b20fbb958	1	0	2023-09-07 11:53:26	2023-09-07 11:53:26
2d832514-d8f8-428c-a5ae-24ccec6fdb86	5ab97801-7ce3-477d-92f9-720b20fbb958	1	1	2023-09-07 17:27:12	2023-09-07 17:27:29
417a3775-e112-4d68-bfde-b02e75f9216a	5ab97801-7ce3-477d-92f9-720b20fbb958	1	0	2023-09-14 01:22:11	2023-09-14 01:22:11
de93fd71-1b98-4aef-b009-bd4828c14310	5ab97801-7ce3-477d-92f9-720b20fbb958	1	0	2023-09-15 11:36:52	2023-09-15 11:36:52
43f1f214-823f-4edc-ac4d-c8ea3415e7da	5ab97801-7ce3-477d-92f9-720b20fbb958	1	0	2023-09-15 16:53:56	2023-09-15 16:53:56
c0d21c34-ac39-4065-b5ac-723119b1a2dd	5ab97801-7ce3-477d-92f9-720b20fbb958	0	1	2023-09-17 13:42:06	2023-09-17 13:42:06
30b3666f-0d5d-4b68-a682-10a011c5df06	5ab97801-7ce3-477d-92f9-720b20fbb958	1	0	2023-09-17 13:51:07	2023-09-17 13:51:07
73b8d25f-341d-4592-bb8d-00d46c008492	5ab97801-7ce3-477d-92f9-720b20fbb958	1	0	2023-09-17 14:22:19	2023-09-17 14:22:19
92088121-2be9-4851-8f4c-41ff2cd9236d	5ab97801-7ce3-477d-92f9-720b20fbb958	1	0	2023-09-17 17:06:16	2023-09-17 17:06:16
\.


--
-- Data for Name: campaigns; Type: TABLE DATA; Schema: public; Owner: trekka
--

COPY public.campaigns (uuid, name, description, vendor_uuid, voucher_type, total_quantity, issuance_costs, original_price, discount, payment_amount, ref_url, status, expire_days, display_in, customer_config, started_at, expired_at, created_at, updated_at) FROM stdin;
6c337fb5-1b8d-4fa5-bebd-2c00c0f4ecfb	Demo Test	\N	5ab97801-7ce3-477d-92f9-720b20fbb958	tour	20	5000	100000	1000	0	\N	running	5	1	{"ages":{"from":"23","to":"55"},"genders":[]}	2023-09-16 00:00:00	2023-09-22 23:59:00	2023-09-16 11:15:34	2023-09-16 11:15:34
fc469cbb-4513-46aa-95f1-dd91a2afd199	Chiến dịch 2	\N	5ab97801-7ce3-477d-92f9-720b20fbb958	tour	200	5000	100000	1000	0	\N	running	5	15	{"ages":{"from":"23","to":"55"},"genders":[]}	2023-09-16 00:00:00	2023-09-22 23:59:00	2023-09-16 18:01:46	2023-09-16 18:01:46
\.


--
-- Data for Name: connect_packages; Type: TABLE DATA; Schema: public; Owner: trekka
--

COPY public.connect_packages (uuid, name, description, connect_count, price, currency, discount, is_default, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: connect_requires; Type: TABLE DATA; Schema: public; Owner: trekka
--

COPY public.connect_requires (uuid, user_uuid, place_uuid, title, description, from_date, to_date, expired_date, budget, status, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: connected_partners; Type: TABLE DATA; Schema: public; Owner: trekka
--

COPY public.connected_partners (uuid, user_uuid, partner_uuid, conversation_uuid, is_activated, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: conversation_members; Type: TABLE DATA; Schema: public; Owner: trekka
--

COPY public.conversation_members (uuid, conversation_uuid, member_uuid, is_owner, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: conversations; Type: TABLE DATA; Schema: public; Owner: trekka
--

COPY public.conversations (uuid, owner_uuid, name, type, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: countries; Type: TABLE DATA; Schema: public; Owner: trekka
--

COPY public.countries (uuid, name, slug, code, local, created_at, updated_at, priority) FROM stdin;
6c6288ab-2ead-4a97-bf4c-98ca4026b282	Việt Nam	viet-nam	VN	\N	2023-09-14 00:05:42	2023-09-14 00:05:42	0
\.


--
-- Data for Name: districts; Type: TABLE DATA; Schema: public; Owner: trekka
--

COPY public.districts (uuid, region_uuid, code, name, slug, type, path_with_type, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: email_tokens; Type: TABLE DATA; Schema: public; Owner: trekka
--

COPY public.email_tokens (uuid, email, type, ref, ref_uuid, token, code, expired_at, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: failed_jobs; Type: TABLE DATA; Schema: public; Owner: trekka
--

COPY public.failed_jobs (id, uuid, connection, queue, payload, exception, failed_at) FROM stdin;
\.


--
-- Data for Name: file_refs; Type: TABLE DATA; Schema: public; Owner: trekka
--

COPY public.file_refs (uuid, file_uuid, ref_uuid, ref, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: files; Type: TABLE DATA; Schema: public; Owner: trekka
--

COPY public.files (uuid, upload_by, privacy, ref, ref_uuid, folder_uuid, date_path, driver, filename, original_filename, filetype, mime, size, extension, title, description, created_at, updated_at) FROM stdin;
99ecf517-23a2-479e-afc0-d9d0af36640c	5ab97801-7ce3-477d-92f9-720b20fbb958	public	gallery	0	0	2023/09/17	disk	brand-6506a0c3c9a67.png	brand.png	image	image/png	10.416015625	png	\N	\N	2023-09-17 13:46:27	2023-09-17 13:46:27
\.


--
-- Data for Name: hobbies; Type: TABLE DATA; Schema: public; Owner: trekka
--

COPY public.hobbies (uuid, name, keywords, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: mbti_details; Type: TABLE DATA; Schema: public; Owner: trekka
--

COPY public.mbti_details (uuid, mbti, description, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: mbti_matches; Type: TABLE DATA; Schema: public; Owner: trekka
--

COPY public.mbti_matches (uuid, first_mbti, second_mbti, score, description, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: metadatas; Type: TABLE DATA; Schema: public; Owner: trekka
--

COPY public.metadatas (uuid, ref, ref_uuid, name, value, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: migrations; Type: TABLE DATA; Schema: public; Owner: trekka
--

COPY public.migrations (id, migration, batch) FROM stdin;
1	2014_10_12_000000_create_users_table	1
2	2014_10_12_100000_create_password_resets_table	1
3	2016_06_01_000001_create_oauth_auth_codes_table	1
4	2016_06_01_000002_create_oauth_access_tokens_table	1
5	2016_06_01_000003_create_oauth_refresh_tokens_table	1
6	2016_06_01_000004_create_oauth_clients_table	1
7	2016_06_01_000005_create_oauth_personal_access_clients_table	1
8	2019_08_19_000000_create_failed_jobs_table	1
9	2019_12_14_000001_create_personal_access_tokens_table	1
10	2022_04_01_120427_create_email_tokens_table	1
11	2022_04_01_194124_create_permission_modules_table	1
12	2022_04_01_194221_create_permission_roles_table	1
13	2022_04_01_194332_create_permission_module_roles_table	1
14	2022_04_01_194506_create_permission_user_roles_table	1
15	2022_04_01_200959_create_notices_table	1
16	2022_04_01_201111_create_user_notices_table	1
17	2022_04_03_043107_create_metadatas_table	1
18	2022_04_03_085016_create_options_table	1
19	2022_04_03_085134_create_option_groups_table	1
20	2022_04_03_085243_create_option_datas_table	1
21	2022_04_07_133114_create_files_table	1
22	2022_04_07_133548_create_file_refs_table	1
23	2022_04_08_201518_create_regions_table	1
24	2022_04_08_201617_create_districts_table	1
25	2022_04_08_201731_create_wards_table	1
26	2022_04_09_202228_create_auth_logs_table	1
27	2022_04_10_061030_create_tags_table	1
28	2022_04_10_061121_create_tag_refs_table	1
29	2022_05_27_115821_create_payment_methods_table	1
30	2022_06_06_120039_create_permission_module_group_actions_table	1
31	2022_09_12_181959_create_payment_transactions_table	1
32	2023_05_05_212652_create_payment_requests_table	1
33	2023_06_29_180412_create_hobbies_table	1
34	2023_06_29_180813_create_user_hobbies_table	1
35	2023_06_30_020531_create_mbti_details_table	1
36	2023_06_30_020845_create_mbti_matches_table	1
37	2023_07_04_102804_create_connected_partners_table	1
38	2023_07_05_151832_create_partner_reviews_table	1
39	2023_07_05_173559_create_partner_reports_table	1
40	2023_07_06_011017_create_countries_table	1
41	2023_07_06_161134_alter_table_regions_add_country_uuid_and_priority	1
42	2023_07_06_162524_alter_table_countries_add_priority	1
43	2023_07_07_024628_create_places_table	1
44	2023_07_07_160411_create_connect_requires_table	1
45	2023_07_07_180357_create_trip_points_table	1
46	2023_07_07_181541_create_require_partner_hobbies_table	1
47	2023_07_12_091735_create_connect_packages_table	1
48	2023_07_21_144803_alter_table_payment_transactions	1
49	2023_07_21_161320_create_posts_table	1
50	2023_08_01_161832_alter_table_payment_transactions_add_connect_count	1
51	2023_08_01_171801_alter_table_payment_requests_add_message	1
52	2023_08_02_005743_alter_table_users_add_agent_expired_date	1
53	2023_08_02_173421_alter_table_payment_transactions_add_is_reported	1
54	2023_08_04_003244_create_agent_payment_logs_table	1
55	2023_08_07_230414_create_conversations_table	1
56	2023_08_07_230625_create_conversation_members_table	1
57	2023_08_14_105557_create_report_logs_table	1
74	2023_09_08_154245_create_campaigns_table	2
75	2023_09_08_175824_create_vouchers_table	2
\.


--
-- Data for Name: notices; Type: TABLE DATA; Schema: public; Owner: trekka
--

COPY public.notices (uuid, created_by, to_uuid, to_group, type, title, message, ref, ref_uuid, seen, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: oauth_access_tokens; Type: TABLE DATA; Schema: public; Owner: trekka
--

COPY public.oauth_access_tokens (id, user_id, client_id, name, scopes, revoked, created_at, updated_at, expires_at) FROM stdin;
322414ad15fbfc7b6a3ef50c616dd27b0f763fbd5668db7f022873077e5eae4d17f7f50a6c96db74	5ab97801-7ce3-477d-92f9-720b20fbb958	9a202bcb-87b6-47b0-80a6-0a050f4e7933	Personal Access Token	[]	f	2023-09-14 01:27:32	2023-09-14 01:27:32	2023-09-29 01:27:32
32eb4692b94e24288ba520d16a9112ccecead41a2a5b937a438856e390466b44c73d62d2fe36bb27	802e09cb-d7de-4621-95e4-69a56436ad15	9a202bcb-87b6-47b0-80a6-0a050f4e7933	Personal Access Token	[]	f	2023-09-17 14:33:09	2023-09-17 14:33:09	2023-10-02 14:33:09
\.


--
-- Data for Name: oauth_auth_codes; Type: TABLE DATA; Schema: public; Owner: trekka
--

COPY public.oauth_auth_codes (id, user_id, client_id, scopes, revoked, expires_at) FROM stdin;
\.


--
-- Data for Name: oauth_clients; Type: TABLE DATA; Schema: public; Owner: trekka
--

COPY public.oauth_clients (id, user_id, name, secret, provider, redirect, personal_access_client, password_client, revoked, created_at, updated_at) FROM stdin;
9a202b9e-82dc-48d3-a188-a73450d06e97	\N	Lover Trips Personal Access Client	72bUe1jOPLZYrFOJoXoe7pyASo1JFPaR7aYkyXJt	\N	http://localhost	t	f	f	2023-09-14 01:26:54	2023-09-14 01:26:54
9a202b9f-6d8b-4c78-bc6f-0d89896f0d49	\N	Lover Trips Password Grant Client	920LQPXs06dz9oKSgImXhweANJQHsk4zjjhfiZy5	users	http://localhost	f	t	f	2023-09-14 01:26:54	2023-09-14 01:26:54
9a202bcb-87b6-47b0-80a6-0a050f4e7933	\N	Lover Trips Personal Access Client	7Vsc9j5aGgAZJ0K2wvVKoTqXMjc8rmePb6uY2xyg	\N	http://localhost	t	f	f	2023-09-14 01:27:23	2023-09-14 01:27:23
9a202bcc-65d4-4077-806c-1f197747e771	\N	Lover Trips Password Grant Client	Uj2DP1iALiFO6UCxvHuOyh2ZG5iSsUO6B2MPsFNB	users	http://localhost	f	t	f	2023-09-14 01:27:24	2023-09-14 01:27:24
\.


--
-- Data for Name: oauth_personal_access_clients; Type: TABLE DATA; Schema: public; Owner: trekka
--

COPY public.oauth_personal_access_clients (id, client_id, created_at, updated_at) FROM stdin;
1	9a202b9e-82dc-48d3-a188-a73450d06e97	2023-09-14 01:26:54	2023-09-14 01:26:54
2	9a202bcb-87b6-47b0-80a6-0a050f4e7933	2023-09-14 01:27:24	2023-09-14 01:27:24
\.


--
-- Data for Name: oauth_refresh_tokens; Type: TABLE DATA; Schema: public; Owner: trekka
--

COPY public.oauth_refresh_tokens (id, access_token_id, revoked, expires_at) FROM stdin;
\.


--
-- Data for Name: option_datas; Type: TABLE DATA; Schema: public; Owner: trekka
--

COPY public.option_datas (uuid, group_uuid, name, label, type, value_type, value, priority, props, can_delete, created_at, updated_at) FROM stdin;
52cf53c1-b6d9-48fc-9924-28fda6c89048	b5aa77dd-c4fc-4143-8409-bad602703635	cache_data_time	Thời gian lưu cache của Dữ liệu từ DB	number	number	\N	1	[]	f	2023-09-07 11:53:11	2023-09-07 11:53:11
26cf50e6-3bbe-46ed-87dc-514ac32cf9c2	b5aa77dd-c4fc-4143-8409-bad602703635	cache_view_time	Thời gian lưu cache của view	number	number	\N	2	[]	f	2023-09-07 11:53:11	2023-09-07 11:53:11
91444221-cc60-4c4a-beb2-21e0b6091de2	b5aa77dd-c4fc-4143-8409-bad602703635	send_mail_notification	Gửi mail thông báo	checkbox	boolean	\N	3	{"template":"switch"}	f	2023-09-07 11:53:11	2023-09-07 11:53:11
456b701d-b3d1-41b2-94ba-24a9789885a8	b5aa77dd-c4fc-4143-8409-bad602703635	mail_notification	Email nhận thông báo (ngăn cách bằng dấu phẩy (,))	maillist	text	\N	4	[]	f	2023-09-07 11:53:11	2023-09-07 11:53:11
87b0127a-dc0a-43a8-8fea-cd476b6ef9f3	1240d404-054d-4232-a4cd-d14f8e73cd4e	mail_driver	Mail Drive	text	text	smtp	1	{"placeholder":"V\\u00ed d\\u1ee5: smtp"}	f	2023-09-07 11:53:11	2023-09-07 11:53:11
fde88565-574f-464c-bd38-1e381d624882	1240d404-054d-4232-a4cd-d14f8e73cd4e	mail_host	Mail host	text	text	smtp.gmail.com	2	{"placeholder":"V\\u00ed d\\u1ee5: smtp.gmail.com"}	f	2023-09-07 11:53:11	2023-09-07 11:53:11
a954e56f-3f1a-471c-9e29-8aad377bd973	1240d404-054d-4232-a4cd-d14f8e73cd4e	mail_port	Mail Port	number	text	587	3	{"placeholder":"V\\u00ed d\\u1ee5: 587"}	f	2023-09-07 11:53:11	2023-09-07 11:53:11
6049e591-c0b0-4687-9b38-80e408a3d949	1240d404-054d-4232-a4cd-d14f8e73cd4e	mail_encryption	Chuẩn mã hóa	text	text	tls	4	{"placeholder":"V\\u00ed d\\u1ee5: tls"}	f	2023-09-07 11:53:11	2023-09-07 11:53:11
3cb7e014-4174-4e4a-aac0-8a7c52e37174	1240d404-054d-4232-a4cd-d14f8e73cd4e	mail_username	Tài khoản đăng nhập mail	text	text	\N	5	{"placeholder":"V\\u00ed d\\u1ee5: example@domain.com"}	f	2023-09-07 11:53:11	2023-09-07 11:53:11
c4f36f65-a797-4fb2-92ce-81617537385b	1240d404-054d-4232-a4cd-d14f8e73cd4e	mail_password	Mật khẩu	password	text	\N	6	{"placeholder":"Nh\\u1eadp m\\u1eadt kh\\u1ea9u \\u0111\\u0103ng nh\\u1eadp email"}	f	2023-09-07 11:53:11	2023-09-07 11:53:11
693288d8-b760-4220-9bb6-7a3a093c77f8	1240d404-054d-4232-a4cd-d14f8e73cd4e	mail_from_address	Email gửi đi (fake)	text	text	example@domain.com	7	{"placeholder":"V\\u00ed d\\u1ee5: example@domain.com"}	f	2023-09-07 11:53:11	2023-09-07 11:53:11
c9238747-da42-4c34-a588-25630f291299	1240d404-054d-4232-a4cd-d14f8e73cd4e	mail_from_name	Tên người gửi	text	text	\N	8	{"placeholder":"V\\u00ed d\\u1ee5: Nguy\\u1ec5n V\\u0103n A ho\\u1eb7c t\\u00ean c\\u00f4ng ty"}	f	2023-09-07 11:53:11	2023-09-07 11:53:11
ca9a53b1-a5fc-42ec-82a9-31b1562c7c62	1240d404-054d-4232-a4cd-d14f8e73cd4e	mail_post	Logo	media	text	\N	9	[]	f	2023-09-07 11:53:11	2023-09-07 11:53:11
075baf0d-a463-4042-840a-3631e00a9fa2	07d7f6b6-49dc-4cb2-8802-a7812fc568f0	slogan	Khẩu hiệu	text	text	Nghĩ lớn - Làm lớn	2	[]	f	2023-09-07 11:53:11	2023-09-07 11:53:11
46b1210a-5978-44b4-9486-ec0e64195e32	07d7f6b6-49dc-4cb2-8802-a7812fc568f0	meta_description	Mô tả website	textarea	text	Đôi nét về website của bạn	12	[]	f	2023-09-07 11:53:11	2023-09-07 11:53:11
7fb4ddf1-1402-43b3-9f73-53a9b151bbbf	849dcb41-d8e8-42d6-a476-0dfc38995d82	agent_lv1	Chiết khấu cho đại lý cấp 1	number	number	0	1	{"min":1,"max":100,"step":0.01}	f	2023-09-07 11:53:11	2023-09-07 11:53:11
a8872182-b35c-4f2a-b527-83c64c834d87	849dcb41-d8e8-42d6-a476-0dfc38995d82	agent_lv2	Chiết khấu cho đại lý cấp 2	number	number	0	2	{"min":1,"max":100,"step":0.01}	f	2023-09-07 11:53:11	2023-09-07 11:53:11
05579d67-56de-4d8f-a159-cf7fdcdd011a	849dcb41-d8e8-42d6-a476-0dfc38995d82	agent_secondary	Chiết khấu gián tiếp  <br /><small>(KH -> Đại lý cấp II -> Đại lý cấp I)</small>	number	number	0	3	{"min":1,"max":100,"step":0.01}	f	2023-09-07 11:53:11	2023-09-07 11:53:11
980dc8ed-8769-4088-9254-cdd338ec6c99	849dcb41-d8e8-42d6-a476-0dfc38995d82	has_ref_discount	Số lượt kết nối cho người mới đăng ký kèm mã khuyến mãi	number	number	0	4	{"min":0,"max":10000}	f	2023-09-07 11:53:11	2023-09-07 11:53:11
b5422e19-a04f-4740-bf13-ca219c9d6667	849dcb41-d8e8-42d6-a476-0dfc38995d82	mon_ref_discount	Số lượt kết nối cho người mới đăng ký KHÔNG có mã khuyến mãi	number	number	0	5	{"min":0,"max":10000}	f	2023-09-07 11:53:11	2023-09-07 11:53:11
98b14b01-d4e0-457e-bee9-6589f53d454d	a59fcfff-278a-4cf0-b43b-b6cd8294debf	facebook	Facebook JS SDK	textarea	text	  <div id="fb-root"></div>\n            <script>(function(d, s, id) {\n              var js, fjs = d.getElementsByTagName(s)[0];\n              if (d.getElementById(id)) return;\n              js = d.createElement(s); js.id = id;\n              js.src = "https://connect.facebook.net/vi_VN/sdk.js#xfbml=1&version=v3.0";\n              fjs.parentNode.insertBefore(js, fjs);\n            }(document, 'script', 'facebook-jssdk'));</script>	1	{"placeholder":"M\\u00e3 SDK t\\u1eeb \\u1ee9ng d\\u1ee5ng c\\u1ee7a b\\u1ea1n","className":"auto-height"}	f	2023-09-07 11:53:11	2023-09-07 11:53:11
76303307-2782-402e-b394-23e8793bf6f9	e1c2ade6-304e-4371-ab06-0a199e66ed7e	domain	domain	text	text	\N	4	{"text":"T\\u00ean mi\\u1ec1n","placeholder":"V\\u00ed d\\u1ee5: domain.com.vn"}	f	2023-09-07 11:53:11	2023-09-07 11:53:11
90d4044f-1bc2-43d5-817e-ba9298ae3612	07d7f6b6-49dc-4cb2-8802-a7812fc568f0	site_name	Tên trang web	text	text	Trekka	1	[]	f	2023-09-07 11:53:11	2023-09-17 13:44:35
b1635700-9aef-40ad-8c6c-160c66ed3264	07d7f6b6-49dc-4cb2-8802-a7812fc568f0	title	Tiêu đề trang	text	text	Trekka	3	[]	f	2023-09-07 11:53:11	2023-09-17 13:44:35
f2a88693-9ede-4004-b42a-fb0f42662486	07d7f6b6-49dc-4cb2-8802-a7812fc568f0	logo	Logo	media	text	\N	4	[]	f	2023-09-07 11:53:11	2023-09-17 13:44:35
77edaedd-c2d5-4cad-8793-3e96db46c9ad	07d7f6b6-49dc-4cb2-8802-a7812fc568f0	mobile_logo	Mobile Logo	media	text	\N	5	{"@type":"image"}	f	2023-09-07 11:53:11	2023-09-17 13:44:35
7f706447-2462-4a48-88c9-03a8cf79250c	07d7f6b6-49dc-4cb2-8802-a7812fc568f0	footer_logo	Footer Logo	media	text	\N	6	[]	f	2023-09-07 11:53:11	2023-09-17 13:44:35
58bae01c-10bb-4485-8e44-79e6a7723cc4	07d7f6b6-49dc-4cb2-8802-a7812fc568f0	favicon	Biệu tượng cho trang web	media	text	\N	8	[]	f	2023-09-07 11:53:11	2023-09-17 13:44:35
c823a358-4cfb-4e26-bca3-34a24c4aa22b	07d7f6b6-49dc-4cb2-8802-a7812fc568f0	admin_page_logo	Logo Trang Admin	media	text	\N	9	[]	f	2023-09-07 11:53:11	2023-09-17 13:44:35
a5e6d26e-a6a9-4e7b-b73a-1676aa4bc64d	07d7f6b6-49dc-4cb2-8802-a7812fc568f0	admin_login_logo	Logo Trang đăng nhập admin	media	text	\N	10	[]	f	2023-09-07 11:53:11	2023-09-17 13:44:35
0697d09d-fcd6-48a5-b6d7-9a8b0e62e3e6	07d7f6b6-49dc-4cb2-8802-a7812fc568f0	meta_title	Tiêu đề seo	text	text	Trekka	11	[]	f	2023-09-07 11:53:11	2023-09-17 13:44:35
459db109-e741-4fcf-a608-8b77384d7585	07d7f6b6-49dc-4cb2-8802-a7812fc568f0	keywords	Từ khóa	text	text	Trekka	13	[]	f	2023-09-07 11:53:11	2023-09-17 13:44:35
ce8ca99c-6a76-4f21-ad50-e4b3643305e8	07d7f6b6-49dc-4cb2-8802-a7812fc568f0	email	Địa chỉ email	email	text	doanln16@gmail.com	14	[]	f	2023-09-07 11:53:11	2023-09-17 13:44:35
465a6b82-e8ad-4a39-9972-6ec1fcb4ebc8	07d7f6b6-49dc-4cb2-8802-a7812fc568f0	phone_number	Số điện thoại	tel	text	0945786960	15	[]	f	2023-09-07 11:53:11	2023-09-17 13:44:35
76324ae9-1b47-4af4-9294-f27a3c500b37	07d7f6b6-49dc-4cb2-8802-a7812fc568f0	address	Địa chỉ	text	text	172, đường Bà Triệu, p. Dân Chủ, tp. Hòa Bình	16	[]	f	2023-09-07 11:53:11	2023-09-17 13:44:35
1b26ea4d-7b7f-4724-80e2-d7b82f1ae129	a59fcfff-278a-4cf0-b43b-b6cd8294debf	twitter	Twitter JS SDK	textarea	text	<script>window.twttr = (function(d, s, id) {\n                var js, fjs = d.getElementsByTagName(s)[0],\n                  t = window.twttr || {};\n                if (d.getElementById(id)) return t;\n                js = d.createElement(s);\n                js.id = id;\n                js.src = "https://platform.twitter.com/widgets.js";\n                fjs.parentNode.insertBefore(js, fjs);\n              \n                t._e = [];\n                t.ready = function(f) {\n                  t._e.push(f);\n                };\n              \n                return t;\n              }(document, "script", "twitter-wjs"));</script>	2	{"placeholder":"M\\u00e3 SDK t\\u1eeb \\u1ee9ng d\\u1ee5ng c\\u1ee7a b\\u1ea1n","className":"auto-height"}	f	2023-09-07 11:53:11	2023-09-07 11:53:11
4425067d-4d96-4d1b-9428-f5bbdabb9e3e	a4370487-4ce9-4b6c-a52d-740e7e4bcd68	apple_touch_icon_57x57	Apple touch icon 57x57	file	text	\N	1	[]	f	2023-09-07 11:53:11	2023-09-07 11:53:11
bb9fb4ba-8386-4701-9132-3c33fc4e4f54	a4370487-4ce9-4b6c-a52d-740e7e4bcd68	apple_touch_icon_60x60	Apple touch icon 60x60	file	text	\N	2	[]	f	2023-09-07 11:53:11	2023-09-07 11:53:11
2c859eba-182c-4b90-b693-375531f9f5ee	a4370487-4ce9-4b6c-a52d-740e7e4bcd68	apple_touch_icon_72x72	Apple touch icon 72x72	file	text	\N	3	[]	f	2023-09-07 11:53:11	2023-09-07 11:53:11
36adb0d8-14ad-49be-9229-2bff1a32461f	a4370487-4ce9-4b6c-a52d-740e7e4bcd68	apple_touch_icon_76x76	Apple touch icon 76x76	file	text	\N	4	[]	f	2023-09-07 11:53:11	2023-09-07 11:53:11
7916500d-27ee-456a-bffa-4a532558090a	a4370487-4ce9-4b6c-a52d-740e7e4bcd68	apple_touch_icon_114x114	Apple touch icon 114x114	file	text	\N	5	[]	f	2023-09-07 11:53:11	2023-09-07 11:53:11
77f843d4-c302-48b1-bf80-b48afed84aba	a4370487-4ce9-4b6c-a52d-740e7e4bcd68	apple_touch_icon_120x120	Apple touch icon 120x120	file	text	\N	6	[]	f	2023-09-07 11:53:11	2023-09-07 11:53:11
3c2cf428-b845-4b0f-9d8d-6c95a66ddcac	a4370487-4ce9-4b6c-a52d-740e7e4bcd68	apple_touch_icon_144x144	Apple touch icon 144x144	file	text	\N	7	[]	f	2023-09-07 11:53:11	2023-09-07 11:53:11
0d453275-0fb3-4daa-99d7-fd072bd23409	a4370487-4ce9-4b6c-a52d-740e7e4bcd68	apple_touch_icon_152x152	Apple touch icon 152x152	file	text	\N	8	[]	f	2023-09-07 11:53:11	2023-09-07 11:53:11
a0e0e8ef-a0b6-40d0-bf93-2514c1670ec9	a4370487-4ce9-4b6c-a52d-740e7e4bcd68	favicon	Biệu tượng cho trang web	file	text		9	[]	f	2023-09-07 11:53:11	2023-09-07 11:53:11
53b57582-8a9b-4c1f-b3de-4d3573172c4a	a4370487-4ce9-4b6c-a52d-740e7e4bcd68	favicon_16x16	Biệu tượng 16x16	file	text		10	[]	f	2023-09-07 11:53:11	2023-09-07 11:53:11
d37d4ba9-9f04-425b-bc91-94f860aa3b0d	a4370487-4ce9-4b6c-a52d-740e7e4bcd68	favicon_32x32	Biệu tượng 32x32	file	text		11	[]	f	2023-09-07 11:53:11	2023-09-07 11:53:11
25fab4be-0614-4e1a-981b-ad5620a4e20d	a4370487-4ce9-4b6c-a52d-740e7e4bcd68	favicon_96x96	Biệu tượng 96x96	file	text		12	[]	f	2023-09-07 11:53:11	2023-09-07 11:53:11
22927373-993f-49d0-b900-36359f31a864	a4370487-4ce9-4b6c-a52d-740e7e4bcd68	favicon_128x128	Biệu tượng 128x128	file	text		13	[]	f	2023-09-07 11:53:11	2023-09-07 11:53:11
d71a407d-477a-42ef-a8a4-03ab51be9e59	a4370487-4ce9-4b6c-a52d-740e7e4bcd68	favicon_196x196	Biệu tượng 196x196	file	text		14	[]	f	2023-09-07 11:53:11	2023-09-07 11:53:11
dc69a210-5650-456f-992a-0419093516f7	a4370487-4ce9-4b6c-a52d-740e7e4bcd68	mstile_144x144	MS Tile 144x144	file	text		15	[]	f	2023-09-07 11:53:11	2023-09-07 11:53:11
9c859da8-c3e3-453d-a81d-c018c20bed18	a4370487-4ce9-4b6c-a52d-740e7e4bcd68	mstile_70x70	MS Tile 70x70	file	text		16	[]	f	2023-09-07 11:53:11	2023-09-07 11:53:11
bc3454c8-6075-4857-826a-dee72300c485	a4370487-4ce9-4b6c-a52d-740e7e4bcd68	mstile_150x150	MS Tile 150x150	file	text		17	[]	f	2023-09-07 11:53:11	2023-09-07 11:53:11
47922e8b-91a1-4c90-922d-d111be61d2c3	a4370487-4ce9-4b6c-a52d-740e7e4bcd68	mstile_310x150	MS Tile 310x150	file	text		18	[]	f	2023-09-07 11:53:11	2023-09-07 11:53:11
32dcc0a8-82b5-4906-8a6f-e3495364e33d	a4370487-4ce9-4b6c-a52d-740e7e4bcd68	mstile_310x310	MS Tile 310x310	file	text		19	[]	f	2023-09-07 11:53:11	2023-09-07 11:53:11
55f40270-17e1-410f-b5d3-87f92a2b0018	e5b451e6-5b44-4d06-abf9-e09ed4681f98	active	Kích hoạt PWA	switch	boolean	\N	1	[]	f	2023-09-07 11:53:11	2023-09-07 11:53:11
f23632c5-5587-4494-9d5a-de12c44fe352	e5b451e6-5b44-4d06-abf9-e09ed4681f98	short_name	Short Name<br><small>(Tên ngắn ngọn 1 - 3 từ)</small>	text	text	\N	2	{"placeholder":"Vi\\u1ebft g\\u00ec \\u0111\\u00f3...","default":"My App"}	f	2023-09-07 11:53:11	2023-09-07 11:53:11
a66fc8f6-0aaa-4d39-9de3-3a5f319e58cd	e5b451e6-5b44-4d06-abf9-e09ed4681f98	name	Name<br><small>(Tên Đẩy dủ)</small>	text	text	\N	3	{"placeholder":"Vi\\u1ebft g\\u00ec \\u0111\\u00f3...","default":"My Web App Full Name"}	f	2023-09-07 11:53:11	2023-09-07 11:53:11
ef8b3667-f507-4885-baea-79bc8da12aa7	e5b451e6-5b44-4d06-abf9-e09ed4681f98	description	Mô tả ngắn	textarea	text	\N	4	{"placeholder":"Vi\\u1ebft g\\u00ec \\u0111\\u00f3...","default":""}	f	2023-09-07 11:53:11	2023-09-07 11:53:11
e356a86a-23f0-464a-bb96-44546152f0eb	e5b451e6-5b44-4d06-abf9-e09ed4681f98	start_url	Đường dẫn nguồn	text	text	\N	5	{"placeholder":"Vi\\u1ebft g\\u00ec \\u0111\\u00f3...","default":"\\/?source=pwa"}	f	2023-09-07 11:53:11	2023-09-07 11:53:11
70d40cac-13d2-4713-86f0-963f62027f87	e5b451e6-5b44-4d06-abf9-e09ed4681f98	scope	Phạm vi truy cập	text	text	\N	6	{"placeholder":"Vi\\u1ebft g\\u00ec \\u0111\\u00f3...","default":"\\/"}	f	2023-09-07 11:53:11	2023-09-07 11:53:11
96e8e48c-27b5-45bf-a454-826a37c2d6d7	e5b451e6-5b44-4d06-abf9-e09ed4681f98	display	Hiển thị	crazyselect	text	\N	7	{"data":{"standalone":"Stand Alone","fullscreen":"Full-Screen","minimal-ui":"Minimal-UI","browser":"Browser"},"default":"standalone"}	f	2023-09-07 11:53:11	2023-09-07 11:53:11
f3ae7523-2c33-475c-a5cd-9c51502b9589	e5b451e6-5b44-4d06-abf9-e09ed4681f98	background_color	Màu nền	colorpicker	text	\N	8	{"default":"#3367D6"}	f	2023-09-07 11:53:11	2023-09-07 11:53:11
615478cf-1807-42db-8998-e1819701fff0	e5b451e6-5b44-4d06-abf9-e09ed4681f98	theme_color	Màu chủ đạo	colorpicker	text	\N	9	{"default":"#3367D6"}	f	2023-09-07 11:53:11	2023-09-07 11:53:11
bb100439-a01e-4d3e-bc4e-f026a546e8fb	e5b451e6-5b44-4d06-abf9-e09ed4681f98	icon_192_png	Biệu tượng 192x192 (png)	media	text	\N	10	{"@type":"image"}	f	2023-09-07 11:53:11	2023-09-07 11:53:11
aab3bcb1-8354-4a87-a427-54ee5b2f0fde	e5b451e6-5b44-4d06-abf9-e09ed4681f98	icon_512_png	Biệu tượng 512x512 (png)	media	text	\N	11	{"@type":"image"}	f	2023-09-07 11:53:11	2023-09-07 11:53:11
aec9ab02-c228-4e64-8fef-ad519637626f	e5b451e6-5b44-4d06-abf9-e09ed4681f98	icon_512_svg	Biệu tượng 512x512 (svg)	media	text	\N	12	{"@type":"image"}	f	2023-09-07 11:53:11	2023-09-07 11:53:11
bd233238-0de6-435d-b571-94f04bcf2179	1679247f-e668-4a32-bb71-c662228ad6ac	https_redirect	Chuyển hướng https	switch	boolean	\N	1	[]	f	2023-09-07 11:53:11	2023-09-07 11:53:11
30209432-aa8c-4d4e-b0e5-ac3dd889b881	e1c2ade6-304e-4371-ab06-0a199e66ed7e	web_type	Thời gian lưu cache của Dữ liệu từ DB	crazyselect	text	default	1	{"call":"get_web_type_options","default":"default"}	f	2023-09-07 11:53:11	2023-09-07 11:53:11
f54dc38a-a5b2-4fcc-ba20-e4526705c1a8	e1c2ade6-304e-4371-ab06-0a199e66ed7e	theme_id	Thời gian lưu cache của Dữ liệu từ DB	crazyselect	number	\N	2	{"call":"get_theme_options"}	f	2023-09-07 11:53:11	2023-09-07 11:53:11
f3f66308-5894-4bc1-a7f8-b956fda6da49	e1c2ade6-304e-4371-ab06-0a199e66ed7e	installed_themes	Danh sách các giao diện đã thiết lập	checklist	text	\N	3	[]	f	2023-09-07 11:53:11	2023-09-07 11:53:11
623788d8-174d-48c1-a6f0-51f00a5ae43e	e1c2ade6-304e-4371-ab06-0a199e66ed7e	alias_domain	alias_domain	text	text	\N	5	{"text":"T\\u00ean mi\\u1ec1n Alias","placeholder":"V\\u00ed d\\u1ee5: domain.com.vn"}	f	2023-09-07 11:53:11	2023-09-07 11:53:11
ee4bcea0-faae-426b-b411-8498367e6054	e1c2ade6-304e-4371-ab06-0a199e66ed7e	ssl	ssl	switch	text	\N	6	{"text":"SSL","check_label":"K\\u00edch ho\\u1ea1t SSL"}	f	2023-09-07 11:53:11	2023-09-07 11:53:11
38888538-6be4-4d4e-9424-b35f025eb1e1	07d7f6b6-49dc-4cb2-8802-a7812fc568f0	web_image	Hình ảnh đại diện cho web	media	text	\N	7	[]	f	2023-09-07 11:53:11	2023-09-17 13:44:35
\.


--
-- Data for Name: option_groups; Type: TABLE DATA; Schema: public; Owner: trekka
--

COPY public.option_groups (uuid, option_uuid, label, slug, config, created_at, updated_at) FROM stdin;
b5aa77dd-c4fc-4143-8409-bad602703635	023f6b57-b1b8-4507-94f2-93e530f27cf0	Cài đặt hệ thống	system	\N	2023-09-07 11:53:11	2023-09-07 11:53:11
1240d404-054d-4232-a4cd-d14f8e73cd4e	023f6b57-b1b8-4507-94f2-93e530f27cf0	Thiết lập Email	mailer	\N	2023-09-07 11:53:11	2023-09-07 11:53:11
07d7f6b6-49dc-4cb2-8802-a7812fc568f0	023f6b57-b1b8-4507-94f2-93e530f27cf0	Thông tin website	siteinfo	\N	2023-09-07 11:53:11	2023-09-07 11:53:11
849dcb41-d8e8-42d6-a476-0dfc38995d82	023f6b57-b1b8-4507-94f2-93e530f27cf0	Thiết lập chiết khấu	discounts	\N	2023-09-07 11:53:11	2023-09-07 11:53:11
a59fcfff-278a-4cf0-b43b-b6cd8294debf	023f6b57-b1b8-4507-94f2-93e530f27cf0	Javascript SDK	jssdk	\N	2023-09-07 11:53:11	2023-09-07 11:53:11
a4370487-4ce9-4b6c-a52d-740e7e4bcd68	023f6b57-b1b8-4507-94f2-93e530f27cf0	Biểu tượng Website	favicons	\N	2023-09-07 11:53:11	2023-09-07 11:53:11
e5b451e6-5b44-4d06-abf9-e09ed4681f98	023f6b57-b1b8-4507-94f2-93e530f27cf0	Thiết lập PWA	pwa	\N	2023-09-07 11:53:11	2023-09-07 11:53:11
1679247f-e668-4a32-bb71-c662228ad6ac	cc6ddf0f-8cb2-4bdf-b89c-6b8e7a259d24	Đường dẫn chung	general	\N	2023-09-07 11:53:11	2023-09-07 11:53:11
8a1f7339-25ca-4b89-84d7-d580c52c8e59	cc6ddf0f-8cb2-4bdf-b89c-6b8e7a259d24	Tin Bài	posts	\N	2023-09-07 11:53:11	2023-09-07 11:53:11
500a2154-47cc-4dd2-adce-e1ebf4674774	cc6ddf0f-8cb2-4bdf-b89c-6b8e7a259d24	Trang Tĩnh	pages	\N	2023-09-07 11:53:11	2023-09-07 11:53:11
5649ec8f-d2fb-4c58-af92-7e79c4e5ad17	cc6ddf0f-8cb2-4bdf-b89c-6b8e7a259d24	Cửa hàng	shop	\N	2023-09-07 11:53:11	2023-09-07 11:53:11
7ca17e02-88dc-4c5a-8c0d-56f01f662028	cc6ddf0f-8cb2-4bdf-b89c-6b8e7a259d24	Dự án	project	\N	2023-09-07 11:53:11	2023-09-07 11:53:11
e1c2ade6-304e-4371-ab06-0a199e66ed7e	416f26ba-a532-43e6-938c-53d024f9f4cf	Cấu hình web	config	\N	2023-09-07 11:53:11	2023-09-07 11:53:11
\.


--
-- Data for Name: options; Type: TABLE DATA; Schema: public; Owner: trekka
--

COPY public.options (uuid, title, slug, ref, ref_uuid, created_at, updated_at) FROM stdin;
023f6b57-b1b8-4507-94f2-93e530f27cf0	Thiết lập	settings	\N	0	2023-09-07 11:53:11	2023-09-07 11:53:11
cc6ddf0f-8cb2-4bdf-b89c-6b8e7a259d24	Thiết lập URL	urlsettings	\N	0	2023-09-07 11:53:11	2023-09-07 11:53:11
416f26ba-a532-43e6-938c-53d024f9f4cf	Web	web	\N	0	2023-09-07 11:53:11	2023-09-07 11:53:11
\.


--
-- Data for Name: partner_reports; Type: TABLE DATA; Schema: public; Owner: trekka
--

COPY public.partner_reports (uuid, reporter_uuid, partner_uuid, point, message, status, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: partner_reviews; Type: TABLE DATA; Schema: public; Owner: trekka
--

COPY public.partner_reviews (uuid, reviewer_uuid, partner_uuid, point, message, is_approved, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: password_resets; Type: TABLE DATA; Schema: public; Owner: trekka
--

COPY public.password_resets (email, token, created_at) FROM stdin;
\.


--
-- Data for Name: payment_methods; Type: TABLE DATA; Schema: public; Owner: trekka
--

COPY public.payment_methods (uuid, name, method, description, guide, config, priority, status, trashed_status, created_at, updated_at) FROM stdin;
63efd541-d725-43e8-8528-abdbfca1eaf7	Thanh toán qua AlePal	alepay	\N	\N	{"token_key":"99d4MhXqVOWtkPsW3zlb1AIUAli17X","checksum_key":"XrdO4qQMKV9irMwX198Vd90L8H1WT5","base_url":"https:\\/\\/alepay-v3-sandbox.nganluong.vn\\/api\\/v3\\/checkout","asset_url":null,"return_url":null,"cancel_url":null}	0	1	0	2023-09-07 11:54:21	2023-09-07 11:54:21
\.


--
-- Data for Name: payment_requests; Type: TABLE DATA; Schema: public; Owner: trekka
--

COPY public.payment_requests (uuid, user_uuid, transaction_uuid, transaction_code, order_uuid, order_code, amount, currency, promotion_code, note, payment_method_name, payment_method_uuid, bank_code, status, finished_at, created_at, updated_at, message) FROM stdin;
\.


--
-- Data for Name: payment_transactions; Type: TABLE DATA; Schema: public; Owner: trekka
--

COPY public.payment_transactions (uuid, user_uuid, method, note, description, type, created_at, updated_at, transaction_uuid, transaction_code, order_uuid, order_code, amount, currency, payment_method_name, payment_method_uuid, current_connect_count, package_connect_count, ref_code, is_reported) FROM stdin;
\.


--
-- Data for Name: permission_module_group_actions; Type: TABLE DATA; Schema: public; Owner: trekka
--

COPY public.permission_module_group_actions (uuid, group_uuid, action_uuid, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: permission_module_roles; Type: TABLE DATA; Schema: public; Owner: trekka
--

COPY public.permission_module_roles (uuid, module_uuid, role_uuid, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: permission_modules; Type: TABLE DATA; Schema: public; Owner: trekka
--

COPY public.permission_modules (uuid, type, name, slug, route, prefix, path, parent_uuid, ref, description, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: permission_roles; Type: TABLE DATA; Schema: public; Owner: trekka
--

COPY public.permission_roles (uuid, name, level, description, handle, return_type, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: permission_user_roles; Type: TABLE DATA; Schema: public; Owner: trekka
--

COPY public.permission_user_roles (uuid, user_uuid, role_uuid, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: personal_access_tokens; Type: TABLE DATA; Schema: public; Owner: trekka
--

COPY public.personal_access_tokens (id, tokenable_type, tokenable_id, name, token, abilities, last_used_at, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: places; Type: TABLE DATA; Schema: public; Owner: trekka
--

COPY public.places (uuid, region_uuid, name, slug, keywords, priority, created_at, updated_at) FROM stdin;
c024890c-88fc-4f27-99b4-a1ee78e24245	85fe3fd6-c084-4651-9862-304d3e05bd0d	Phố cổ Đồng Văn	pho-co-dong-van	Phố cổ Đồng Văn, pho co dong van, pho-co-dong-van, phocodongvan	10	2023-09-14 00:28:44	2023-09-14 00:28:44
4ca3ba0d-fb62-4d3a-948c-aa6f13036aae	85fe3fd6-c084-4651-9862-304d3e05bd0d	Đèo Mã Pí Lèng	deo-ma-pi-leng	Đèo Mã Pí Lèng, deo ma pi leng, deo-ma-pi-leng, deomapileng	10	2023-09-14 00:28:44	2023-09-14 00:28:44
8307d71b-ff91-4a3c-817b-02de0cbf329e	85fe3fd6-c084-4651-9862-304d3e05bd0d	Thị trấn Phó bảng – Yên Minh	thi-tran-pho-bang-yen-minh	Thị trấn Phó bảng – Yên Minh, thi tran pho bang – yen minh, thi-tran-pho-bang-yen-minh, thitranphobangyenminh	10	2023-09-14 00:28:44	2023-09-14 00:28:44
53718cee-b2fb-424c-ade0-a290069af618	85fe3fd6-c084-4651-9862-304d3e05bd0d	Rừng thông Yên Minh	rung-thong-yen-minh	Rừng thông Yên Minh, rung thong yen minh, rung-thong-yen-minh, rungthongyenminh	10	2023-09-14 00:28:45	2023-09-14 00:28:45
f7549fc1-9180-453c-b53c-75720c4f82e5	85fe3fd6-c084-4651-9862-304d3e05bd0d	Thung Lũng Sủng Là	thung-lung-sung-la	Thung Lũng Sủng Là, thung lung sung la, thung-lung-sung-la, thunglungsungla	10	2023-09-14 00:28:45	2023-09-14 00:28:45
a27cfef9-f6a7-41a8-9f26-cf85f961df4a	85fe3fd6-c084-4651-9862-304d3e05bd0d	Dinh thự Họ Vương – Biệt thự Vua Mèo	dinh-thu-ho-vuong-biet-thu-vua-meo	Dinh thự Họ Vương – Biệt thự Vua Mèo, dinh thu ho vuong – biet thu vua meo, dinh-thu-ho-vuong-biet-thu-vua-meo, dinhthuhovuongbietthuvuameo	10	2023-09-14 00:28:45	2023-09-14 00:28:45
c5e20a51-d18b-4dcc-817e-a8cd0377a284	85fe3fd6-c084-4651-9862-304d3e05bd0d	Núi Quản Bạ	nui-quan-ba	Núi Quản Bạ, nui quan ba, nui-quan-ba, nuiquanba	10	2023-09-14 00:28:45	2023-09-14 00:28:45
6a87c791-105c-4885-a018-942f3bd02c35	85fe3fd6-c084-4651-9862-304d3e05bd0d	Chợ Lùi	cho-lui	Chợ Lùi, cho lui, cho-lui, cholui	10	2023-09-14 00:28:45	2023-09-14 00:28:45
7bc07d20-73c6-4663-930d-60cd74327363	85fe3fd6-c084-4651-9862-304d3e05bd0d	Hoàng Su Phì	hoang-su-phi	Hoàng Su Phì, hoang su phi, hoang-su-phi, hoangsuphi	10	2023-09-14 00:28:46	2023-09-14 00:28:46
00522f53-7277-44e8-bc16-6539dd4ec1cf	85fe3fd6-c084-4651-9862-304d3e05bd0d	Núi Cấm Sơn	nui-cam-son	Núi Cấm Sơn, nui cam son, nui-cam-son, nuicamson	10	2023-09-14 00:28:46	2023-09-14 00:28:46
ff51681a-415d-47d1-b360-5cd9004f261b	85fe3fd6-c084-4651-9862-304d3e05bd0d	Thác Yên Bình	thac-yen-binh	Thác Yên Bình, thac yen binh, thac-yen-binh, thacyenbinh	10	2023-09-14 00:28:46	2023-09-14 00:28:46
31d1abcd-f925-4535-a2fe-bfa2016c104c	44b1fe0e-a95a-4853-8833-7e86638a3b3b	Động Ngườm Ngao	dong-nguom-ngao	Động Ngườm Ngao, dong nguom ngao, dong-nguom-ngao, dongnguomngao	10	2023-09-14 00:28:46	2023-09-14 00:28:46
95555756-ef11-4f55-8af8-73260ca01c4e	44b1fe0e-a95a-4853-8833-7e86638a3b3b	Thác Bản Giốc	thac-ban-gioc	Thác Bản Giốc, thac ban gioc, thac-ban-gioc, thacbangioc	10	2023-09-14 00:28:46	2023-09-14 00:28:46
0bb74c62-0036-4dde-a39e-586c049ce10e	44b1fe0e-a95a-4853-8833-7e86638a3b3b	Lũng Luông	lung-luong	Lũng Luông, lung luong, lung-luong, lungluong	10	2023-09-14 00:28:47	2023-09-14 00:28:47
0526df17-382c-48d3-b0f6-46cfd32e150c	44b1fe0e-a95a-4853-8833-7e86638a3b3b	Phia Đén	phia-den	Phia Đén, phia den, phia-den, phiaden	10	2023-09-14 00:28:47	2023-09-14 00:28:47
1920162e-bce1-4b46-985b-95cef565b824	44b1fe0e-a95a-4853-8833-7e86638a3b3b	Khu di tích anh Kim Đồng	khu-di-tich-anh-kim-dong	Khu di tích anh Kim Đồng, khu di tich anh kim dong, khu-di-tich-anh-kim-dong, khuditichanhkimdong	10	2023-09-14 00:28:47	2023-09-14 00:28:47
1ab4238a-66fd-49b7-b520-4534cdbb45db	44b1fe0e-a95a-4853-8833-7e86638a3b3b	Di tích Pắc Bó	di-tich-pac-bo	Di tích Pắc Bó, di tich pac bo, di-tich-pac-bo, ditichpacbo	10	2023-09-14 00:28:47	2023-09-14 00:28:47
fb3e8cd2-2c06-42e2-b85f-8bda4fd36c06	44b1fe0e-a95a-4853-8833-7e86638a3b3b	Hồ Thang Hen	ho-thang-hen	Hồ Thang Hen, ho thang hen, ho-thang-hen, hothanghen	10	2023-09-14 00:28:47	2023-09-14 00:28:47
e9ebf27d-a5e9-4298-a667-79a5ac0e6f8f	44b1fe0e-a95a-4853-8833-7e86638a3b3b	Làng rèn Phúc Sen	lang-ren-phuc-sen	Làng rèn Phúc Sen, lang ren phuc sen, lang-ren-phuc-sen, langrenphucsen	10	2023-09-14 00:28:48	2023-09-14 00:28:48
936d4666-a4a6-4ded-ae8e-d921c856f4ea	44b1fe0e-a95a-4853-8833-7e86638a3b3b	Di tích chiến thắng chiến dịch biên giới 1950	di-tich-chien-thang-chien-dich-bien-gioi-1950	Di tích chiến thắng chiến dịch biên giới 1950, di tich chien thang chien dich bien gioi 1950, di-tich-chien-thang-chien-dich-bien-gioi-1950, ditichchienthangchiendichbiengioi1950	10	2023-09-14 00:28:48	2023-09-14 00:28:48
0980d2f7-c9ce-4cac-9228-478ae8c1cdda	44b1fe0e-a95a-4853-8833-7e86638a3b3b	Khu di tích Quốc gia đặc biệt rừng Trần Hưng Đạo.	khu-di-tich-quoc-gia-dac-biet-rung-tran-hung-dao	Khu di tích Quốc gia đặc biệt rừng Trần Hưng Đạo., khu di tich quoc gia dac biet rung tran hung dao., khu-di-tich-quoc-gia-dac-biet-rung-tran-hung-dao, khuditichquocgiadacbietrungtranhungdao	10	2023-09-14 00:28:48	2023-09-14 00:28:48
f3af8c45-c1ec-4da5-83b7-bd5232713e19	15699e99-ede1-45d5-b766-42f474693397	Núi Pắc Ta	nui-pac-ta	Núi Pắc Ta, nui pac ta, nui-pac-ta, nuipacta	10	2023-09-14 00:28:48	2023-09-14 00:28:48
49d98c03-6a04-414e-911b-c02bb0f38be0	15699e99-ede1-45d5-b766-42f474693397	Đền Thương	den-thuong	Đền Thương, den thuong, den-thuong, denthuong	10	2023-09-14 00:28:48	2023-09-14 00:28:48
fe0efd25-bce4-4dbf-b138-a5c9ee187de1	15699e99-ede1-45d5-b766-42f474693397	Đền Hạ	den-ha	Đền Hạ, den ha, den-ha, denha	10	2023-09-14 00:28:49	2023-09-14 00:28:49
931b678c-933f-4872-8b02-a96f19955d26	15699e99-ede1-45d5-b766-42f474693397	Thác Pác Ban	thac-pac-ban	Thác Pác Ban, thac pac ban, thac-pac-ban, thacpacban	10	2023-09-14 00:28:49	2023-09-14 00:28:49
3b9f8c13-826b-4f32-90b8-93e246459ab2	15699e99-ede1-45d5-b766-42f474693397	Suối khoáng Mỹ Lâm	suoi-khoang-my-lam	Suối khoáng Mỹ Lâm, suoi khoang my lam, suoi-khoang-my-lam, suoikhoangmylam	10	2023-09-14 00:28:49	2023-09-14 00:28:49
687e1897-337e-4cca-a77f-8769df89ea13	15699e99-ede1-45d5-b766-42f474693397	Thác Mơ	thac-mo	Thác Mơ, thac mo, thac-mo, thacmo	10	2023-09-14 00:28:49	2023-09-14 00:28:49
c2eb734c-ea2a-48ec-9a03-5d93cad42d97	15699e99-ede1-45d5-b766-42f474693397	Vườn Hoa Lê Hồng Thái	vuon-hoa-le-hong-thai	Vườn Hoa Lê Hồng Thái, vuon hoa le hong thai, vuon-hoa-le-hong-thai, vuonhoalehongthai	10	2023-09-14 00:28:49	2023-09-14 00:28:49
c18840ca-296f-42dd-8f22-377ae6ae70b0	15699e99-ede1-45d5-b766-42f474693397	Khu du lịch sinh thái Thác Lăn	khu-du-lich-sinh-thai-thac-lan	Khu du lịch sinh thái Thác Lăn, khu du lich sinh thai thac lan, khu-du-lich-sinh-thai-thac-lan, khudulichsinhthaithaclan	10	2023-09-14 00:28:49	2023-09-14 00:28:49
43bcc7ae-5403-4cf8-bb9e-3da3954e797a	15699e99-ede1-45d5-b766-42f474693397	Khu du lịch sinh Thái Hàm Yên	khu-du-lich-sinh-thai-ham-yen	Khu du lịch sinh Thái Hàm Yên, khu du lich sinh thai ham yen, khu-du-lich-sinh-thai-ham-yen, khudulichsinhthaihamyen	10	2023-09-14 00:28:50	2023-09-14 00:28:50
0ae0c7d2-1d95-4a39-9511-e20e09febf38	15699e99-ede1-45d5-b766-42f474693397	Khu du lịch sinh thái Nà Hàng	khu-du-lich-sinh-thai-na-hang	Khu du lịch sinh thái Nà Hàng, khu du lich sinh thai na hang, khu-du-lich-sinh-thai-na-hang, khudulichsinhthainahang	10	2023-09-14 00:28:50	2023-09-14 00:28:50
32abd36e-ca58-4ddd-9e20-d2003215727c	51e03f09-3152-433f-ab48-3512d3643b0d	Hồ Ba Bể	ho-ba-be	Hồ Ba Bể, ho ba be, ho-ba-be, hobabe	10	2023-09-14 00:28:50	2023-09-14 00:28:50
22488af7-c04c-4be5-8ab6-784b306c0113	51e03f09-3152-433f-ab48-3512d3643b0d	Vườn Quốc Gia Ba Bể	vuon-quoc-gia-ba-be	Vườn Quốc Gia Ba Bể, vuon quoc gia ba be, vuon-quoc-gia-ba-be, vuonquocgiababe	10	2023-09-14 00:28:50	2023-09-14 00:28:50
a99e0014-85f5-43aa-a877-e3b90f9ea857	51e03f09-3152-433f-ab48-3512d3643b0d	Động Nàng Tiên	dong-nang-tien	Động Nàng Tiên, dong nang tien, dong-nang-tien, dongnangtien	10	2023-09-14 00:28:50	2023-09-14 00:28:50
42de8cba-b9bb-4ac5-b44a-2e6f7cd9cb4f	51e03f09-3152-433f-ab48-3512d3643b0d	Động Puông	dong-puong	Động Puông, dong puong, dong-puong, dongpuong	10	2023-09-14 00:28:51	2023-09-14 00:28:51
8a08688c-0529-43e8-850f-c67d9ca31ae6	51e03f09-3152-433f-ab48-3512d3643b0d	Thác Đầu Đẳng	thac-dau-dang	Thác Đầu Đẳng, thac dau dang, thac-dau-dang, thacdaudang	10	2023-09-14 00:28:51	2023-09-14 00:28:51
e16f9bdf-969f-466a-9f60-58134a12e63b	51e03f09-3152-433f-ab48-3512d3643b0d	Chùa Thạch Long	chua-thach-long	Chùa Thạch Long, chua thach long, chua-thach-long, chuathachlong	10	2023-09-14 00:28:51	2023-09-14 00:28:51
c9d57a38-65b6-4158-9b62-1f22380b5f17	20c05720-d47b-46a3-b0e3-b6a5901153c6	Nàng Tô Thị	nang-to-thi	Nàng Tô Thị, nang to thi, nang-to-thi, nangtothi	10	2023-09-14 00:28:51	2023-09-14 00:28:51
4546587d-599c-4d8b-89e6-196d85c40940	20c05720-d47b-46a3-b0e3-b6a5901153c6	Cột cờ Phai Vê	cot-co-phai-ve	Cột cờ Phai Vê, cot co phai ve, cot-co-phai-ve, cotcophaive	10	2023-09-14 00:28:52	2023-09-14 00:28:52
371b36ec-b4e9-4b0e-a744-6e1e639661ea	20c05720-d47b-46a3-b0e3-b6a5901153c6	Thành nhà Mạc	thanh-nha-mac	Thành nhà Mạc, thanh nha mac, thanh-nha-mac, thanhnhamac	10	2023-09-14 00:28:52	2023-09-14 00:28:52
0cabdad7-4134-4cae-a4f4-6f3bb8af5b75	20c05720-d47b-46a3-b0e3-b6a5901153c6	Ải Chi Lăng	ai-chi-lang	Ải Chi Lăng, ai chi lang, ai-chi-lang, aichilang	10	2023-09-14 00:28:52	2023-09-14 00:28:52
96fca1a0-96a6-4a83-a1f4-4562d02d7542	20c05720-d47b-46a3-b0e3-b6a5901153c6	Đền Kỳ Cùng	den-ky-cung	Đền Kỳ Cùng, den ky cung, den-ky-cung, denkycung	10	2023-09-14 00:28:52	2023-09-14 00:28:52
525ac6a9-8d84-4e0b-aea3-89cf8f28a9e3	20c05720-d47b-46a3-b0e3-b6a5901153c6	Đền Mẫu Đồng Đăng	den-mau-dong-dang	Đền Mẫu Đồng Đăng, den mau dong dang, den-mau-dong-dang, denmaudongdang	10	2023-09-14 00:28:52	2023-09-14 00:28:52
1402c516-1046-4c6d-8b30-a33ff0fc3e24	20c05720-d47b-46a3-b0e3-b6a5901153c6	Chợ Đông Kinh	cho-dong-kinh	Chợ Đông Kinh, cho dong kinh, cho-dong-kinh, chodongkinh	10	2023-09-14 00:28:52	2023-09-14 00:28:52
489d1995-7132-473e-8b9b-06c646c7faa3	20c05720-d47b-46a3-b0e3-b6a5901153c6	Bắc Lê	bac-le	Bắc Lê, bac le, bac-le, bacle	10	2023-09-14 00:28:53	2023-09-14 00:28:53
6be37f15-d28d-4aa9-a128-1bebd38ab5db	20c05720-d47b-46a3-b0e3-b6a5901153c6	Đông Nhi Thanh	dong-nhi-thanh	Đông Nhi Thanh, dong nhi thanh, dong-nhi-thanh, dongnhithanh	10	2023-09-14 00:28:53	2023-09-14 00:28:53
37bf48b5-7ea1-484a-98d8-18beed875bb0	20c05720-d47b-46a3-b0e3-b6a5901153c6	Mẫu Sơn	mau-son	Mẫu Sơn, mau son, mau-son, mauson	10	2023-09-14 00:28:53	2023-09-14 00:28:53
838f07dd-dd4d-4b7a-a801-0ce995c1a385	95a8bd75-3d06-442d-865e-1efa20d55c8b	Hồ Núi Cốc	ho-nui-coc	Hồ Núi Cốc, ho nui coc, ho-nui-coc, honuicoc	10	2023-09-14 00:28:53	2023-09-14 00:28:53
6ab0a802-3344-4801-aabd-1d5cf5f9073a	95a8bd75-3d06-442d-865e-1efa20d55c8b	Khu an toàn kháng chiến ATK	khu-an-toan-khang-chien-atk	Khu an toàn kháng chiến ATK, khu an toan khang chien atk, khu-an-toan-khang-chien-atk, khuantoankhangchienatk	10	2023-09-14 00:28:53	2023-09-14 00:28:53
40d9d446-f09f-4d91-96bf-21ad036787a0	95a8bd75-3d06-442d-865e-1efa20d55c8b	Bảo tàng dân tộc Việt Nam	bao-tang-dan-toc-viet-nam	Bảo tàng dân tộc Việt Nam, bao tang dan toc viet nam, bao-tang-dan-toc-viet-nam, baotangdantocvietnam	10	2023-09-14 00:28:54	2023-09-14 00:28:54
b9d43eb1-3efa-4e10-b63c-46a4adf58bd4	95a8bd75-3d06-442d-865e-1efa20d55c8b	Thác Nậm Dứt	thac-nam-dut	Thác Nậm Dứt, thac nam dut, thac-nam-dut, thacnamdut	10	2023-09-14 00:28:54	2023-09-14 00:28:54
f5cc6894-9fe1-4cea-b6ae-c937a4eb8f22	95a8bd75-3d06-442d-865e-1efa20d55c8b	Động Linh Sơn	dong-linh-son	Động Linh Sơn, dong linh son, dong-linh-son, donglinhson	10	2023-09-14 00:28:54	2023-09-14 00:28:54
f9fd2ed5-242e-4b92-856f-9d5b79cd7304	95a8bd75-3d06-442d-865e-1efa20d55c8b	Suối Mỏ Gà	suoi-mo-ga	Suối Mỏ Gà, suoi mo ga, suoi-mo-ga, suoimoga	10	2023-09-14 00:28:54	2023-09-14 00:28:54
452f646b-b687-4334-8747-06f04d1ac015	95a8bd75-3d06-442d-865e-1efa20d55c8b	Hang Phượng Hoàng	hang-phuong-hoang	Hang Phượng Hoàng, hang phuong hoang, hang-phuong-hoang, hangphuonghoang	10	2023-09-14 00:28:54	2023-09-14 00:28:54
90408360-eeb3-4f78-bc05-f804d073bd4d	95a8bd75-3d06-442d-865e-1efa20d55c8b	Đền Đuổm	den-duom	Đền Đuổm, den duom, den-duom, denduom	10	2023-09-14 00:28:55	2023-09-14 00:28:55
82931f46-d91b-40b6-914a-aed10b760ec6	95a8bd75-3d06-442d-865e-1efa20d55c8b	Đồi chè Tân Cương	doi-che-tan-cuong	Đồi chè Tân Cương, doi che tan cuong, doi-che-tan-cuong, doichetancuong	10	2023-09-14 00:28:55	2023-09-14 00:28:55
af9f6ee2-3b47-43d6-a8fc-c9d56078322c	95a8bd75-3d06-442d-865e-1efa20d55c8b	Hang động Đồng Hỷ – Võ Nhai	hang-dong-dong-hy-vo-nhai	Hang động Đồng Hỷ – Võ Nhai, hang dong dong hy – vo nhai, hang-dong-dong-hy-vo-nhai, hangdongdonghyvonhai	10	2023-09-14 00:28:55	2023-09-14 00:28:55
6e814b56-f251-4096-9da9-68bc5dde5a27	5fd6643b-ac12-4e23-a4cc-886cb9754f09	Khu di tích Khuôn Thần	khu-di-tich-khuon-than	Khu di tích Khuôn Thần, khu di tich khuon than, khu-di-tich-khuon-than, khuditichkhuonthan	10	2023-09-14 00:28:55	2023-09-14 00:28:55
0aeb782e-9f87-4982-ab3e-8a3d1b36fe59	5fd6643b-ac12-4e23-a4cc-886cb9754f09	Rừng nguyên sinh Khe Rỗ	rung-nguyen-sinh-khe-ro	Rừng nguyên sinh Khe Rỗ, rung nguyen sinh khe ro, rung-nguyen-sinh-khe-ro, rungnguyensinhkhero	10	2023-09-14 00:28:56	2023-09-14 00:28:56
43131ff8-e5c6-4452-81c0-5042b279d839	5fd6643b-ac12-4e23-a4cc-886cb9754f09	Đền Suối Mỡ	den-suoi-mo	Đền Suối Mỡ, den suoi mo, den-suoi-mo, densuoimo	10	2023-09-14 00:28:56	2023-09-14 00:28:56
0ddc35fe-e2da-4c11-8b73-9ad224d71585	5fd6643b-ac12-4e23-a4cc-886cb9754f09	Khu di tích Suối Mỡ	khu-di-tich-suoi-mo	Khu di tích Suối Mỡ, khu di tich suoi mo, khu-di-tich-suoi-mo, khuditichsuoimo	10	2023-09-14 00:28:56	2023-09-14 00:28:56
d64fdc4f-0fae-4a4f-aa42-4ea51259e21e	5fd6643b-ac12-4e23-a4cc-886cb9754f09	Chùa Đức La	chua-duc-la	Chùa Đức La, chua duc la, chua-duc-la, chuaducla	10	2023-09-14 00:28:56	2023-09-14 00:28:56
198e4e6a-cc59-4b62-8acd-78b41ff4dbfc	5fd6643b-ac12-4e23-a4cc-886cb9754f09	Đình Thổ Hà	dinh-tho-ha	Đình Thổ Hà, dinh tho ha, dinh-tho-ha, dinhthoha	10	2023-09-14 00:28:56	2023-09-14 00:28:56
c6d5a5ba-c6f9-4ce4-abb4-a375eeaddc37	5fd6643b-ac12-4e23-a4cc-886cb9754f09	Hồ Cấm Sơn	ho-cam-son	Hồ Cấm Sơn, ho cam son, ho-cam-son, hocamson	10	2023-09-14 00:28:56	2023-09-14 00:28:56
56376eec-5b0d-4408-9cf9-257e03ee6bdc	5fd6643b-ac12-4e23-a4cc-886cb9754f09	Làng rượu Vân Hà	lang-ruou-van-ha	Làng rượu Vân Hà, lang ruou van ha, lang-ruou-van-ha, langruouvanha	10	2023-09-14 00:28:57	2023-09-14 00:28:57
e33a3787-8f8f-4435-a1a0-a80b80927667	5fd6643b-ac12-4e23-a4cc-886cb9754f09	Thành cổ Xương Giang	thanh-co-xuong-giang	Thành cổ Xương Giang, thanh co xuong giang, thanh-co-xuong-giang, thanhcoxuonggiang	10	2023-09-14 00:28:57	2023-09-14 00:28:57
c4ca8b80-db4e-45ee-bf6c-648e0c869761	0c13c812-d0fb-4dd3-9396-4ec45660784b	Vịnh Hạ Long	vinh-ha-long	Vịnh Hạ Long, vinh ha long, vinh-ha-long, vinhhalong	10	2023-09-14 00:28:57	2023-09-14 00:28:57
83caa511-9f5c-4a66-a641-b5e00430cd8f	0c13c812-d0fb-4dd3-9396-4ec45660784b	Đảo Tuần Châu	dao-tuan-chau	Đảo Tuần Châu, dao tuan chau, dao-tuan-chau, daotuanchau	10	2023-09-14 00:28:57	2023-09-14 00:28:57
6671080c-32db-493f-89ce-57b00429a5cb	0c13c812-d0fb-4dd3-9396-4ec45660784b	Đảo Cô Tô	dao-co-to	Đảo Cô Tô, dao co to, dao-co-to, daocoto	10	2023-09-14 00:28:57	2023-09-14 00:28:57
140de9b7-f18c-40ed-9dd9-021333ec2819	0c13c812-d0fb-4dd3-9396-4ec45660784b	Đảo Quan Lạn	dao-quan-lan	Đảo Quan Lạn, dao quan lan, dao-quan-lan, daoquanlan	10	2023-09-14 00:28:58	2023-09-14 00:28:58
2e1d088d-1cda-437c-afb3-e137d2cef13d	0c13c812-d0fb-4dd3-9396-4ec45660784b	Bãi Biển Trà Cổ	bai-bien-tra-co	Bãi Biển Trà Cổ, bai bien tra co, bai-bien-tra-co, baibientraco	10	2023-09-14 00:28:58	2023-09-14 00:28:58
21dbe431-e193-487f-992f-b35f0ee65cb7	0c13c812-d0fb-4dd3-9396-4ec45660784b	Bãi Cháy	bai-chay	Bãi Cháy, bai chay, bai-chay, baichay	10	2023-09-14 00:28:58	2023-09-14 00:28:58
eceffbc7-7eb2-4f23-981b-598155075b08	0c13c812-d0fb-4dd3-9396-4ec45660784b	Núi Yên Tử	nui-yen-tu	Núi Yên Tử, nui yen tu, nui-yen-tu, nuiyentu	10	2023-09-14 00:28:58	2023-09-14 00:28:58
95fbc679-1593-4ad1-bfb9-57b7f485a6e8	0c13c812-d0fb-4dd3-9396-4ec45660784b	Khu vui chơi Sun World Quảng Ninh	khu-vui-choi-sun-world-quang-ninh	Khu vui chơi Sun World Quảng Ninh, khu vui choi sun world quang ninh, khu-vui-choi-sun-world-quang-ninh, khuvuichoisunworldquangninh	10	2023-09-14 00:28:58	2023-09-14 00:28:58
c05c8edd-220a-426e-9300-27084b379554	8c45434f-b696-42e1-bf43-e393a526d824	Khu di tích đền Hùng	khu-di-tich-den-hung	Khu di tích đền Hùng, khu di tich den hung, khu-di-tich-den-hung, khuditichdenhung	10	2023-09-14 00:28:59	2023-09-14 00:28:59
07d0669a-37a8-40dd-bbba-1f8a840fb4cc	8c45434f-b696-42e1-bf43-e393a526d824	Đền Quốc Mẫu Âu Cơ	den-quoc-mau-au-co	Đền Quốc Mẫu Âu Cơ, den quoc mau au co, den-quoc-mau-au-co, denquocmauauco	10	2023-09-14 00:28:59	2023-09-14 00:28:59
632b4e4d-67d1-403e-844a-dafc25494a7d	8c45434f-b696-42e1-bf43-e393a526d824	Ao giời	ao-gioi	Ao giời, ao gioi, ao-gioi, aogioi	10	2023-09-14 00:28:59	2023-09-14 00:28:59
c63e479d-5b5f-4036-aa87-7ff1a8b890e4	8c45434f-b696-42e1-bf43-e393a526d824	Vườn quốc gia Xuân Sơn	vuon-quoc-gia-xuan-son	Vườn quốc gia Xuân Sơn, vuon quoc gia xuan son, vuon-quoc-gia-xuan-son, vuonquocgiaxuanson	10	2023-09-14 00:28:59	2023-09-14 00:28:59
43711749-255e-4370-96a5-53703293dc67	8c45434f-b696-42e1-bf43-e393a526d824	Đồi chè Long Cốc	doi-che-long-coc	Đồi chè Long Cốc, doi che long coc, doi-che-long-coc, doichelongcoc	10	2023-09-14 00:28:59	2023-09-14 00:28:59
406cd534-22aa-4fab-85e2-e2244212d4db	8c45434f-b696-42e1-bf43-e393a526d824	Hang Lạng	hang-lang	Hang Lạng, hang lang, hang-lang, hanglang	10	2023-09-14 00:29:00	2023-09-14 00:29:00
7db8306f-f917-4dfd-b5ea-1fdddf2a1248	8c45434f-b696-42e1-bf43-e393a526d824	Khu du lịch nước khoáng nóng Sơn Thủy	khu-du-lich-nuoc-khoang-nong-son-thuy	Khu du lịch nước khoáng nóng Sơn Thủy, khu du lich nuoc khoang nong son thuy, khu-du-lich-nuoc-khoang-nong-son-thuy, khudulichnuockhoangnongsonthuy	10	2023-09-14 00:29:00	2023-09-14 00:29:00
5cbf24fa-a84b-4a27-9346-2d38ae6cdb0a	3f11ff64-f2d8-40bd-8dcb-25fa3e2fd448	Đèo Ô Quy Hồ	deo-o-quy-ho	Đèo Ô Quy Hồ, deo o quy ho, deo-o-quy-ho, deooquyho	10	2023-09-14 00:29:00	2023-09-14 00:29:00
c1a74605-9ffe-4f14-9e46-f544fed39fa0	3f11ff64-f2d8-40bd-8dcb-25fa3e2fd448	Cao Nguyên Sìn Hồ	cao-nguyen-sin-ho	Cao Nguyên Sìn Hồ, cao nguyen sin ho, cao-nguyen-sin-ho, caonguyensinho	10	2023-09-14 00:29:00	2023-09-14 00:29:00
675d5c8f-f161-497a-9b72-12743ade58ce	3f11ff64-f2d8-40bd-8dcb-25fa3e2fd448	Ngã Ba sông Đà – Nậm Na	nga-ba-song-da-nam-na	Ngã Ba sông Đà – Nậm Na, nga ba song da – nam na, nga-ba-song-da-nam-na, ngabasongdanamna	10	2023-09-14 00:29:01	2023-09-14 00:29:01
6bed62e4-00e6-4427-bf5f-4d0509a3c33d	3f11ff64-f2d8-40bd-8dcb-25fa3e2fd448	Đỉnh Chu Va	dinh-chu-va	Đỉnh Chu Va, dinh chu va, dinh-chu-va, dinhchuva	10	2023-09-14 00:29:01	2023-09-14 00:29:01
0011ab4a-981f-4cd3-93fe-e2610fc52261	3f11ff64-f2d8-40bd-8dcb-25fa3e2fd448	Cánh Đồng Mường Than	canh-dong-muong-than	Cánh Đồng Mường Than, canh dong muong than, canh-dong-muong-than, canhdongmuongthan	10	2023-09-14 00:29:01	2023-09-14 00:29:01
518bb564-010f-46da-82bf-1790616c8638	3f11ff64-f2d8-40bd-8dcb-25fa3e2fd448	Núi Nam Pu Ta Leng – Phong Thổ	nui-nam-pu-ta-leng-phong-tho	Núi Nam Pu Ta Leng – Phong Thổ, nui nam pu ta leng – phong tho, nui-nam-pu-ta-leng-phong-tho, nuinamputalengphongtho	10	2023-09-14 00:29:01	2023-09-14 00:29:01
c07f2fb1-23f7-461b-b097-89f1c0e31fbf	3f11ff64-f2d8-40bd-8dcb-25fa3e2fd448	Pu Si Lung – Mường Tè	pu-si-lung-muong-te	Pu Si Lung – Mường Tè, pu si lung – muong te, pu-si-lung-muong-te, pusilungmuongte	10	2023-09-14 00:29:01	2023-09-14 00:29:01
5d83e49f-365f-4f8c-90bb-1fe6cbf9554f	3f11ff64-f2d8-40bd-8dcb-25fa3e2fd448	Đỉnh Tả Liên – Phong Thổ	dinh-ta-lien-phong-tho	Đỉnh Tả Liên – Phong Thổ, dinh ta lien – phong tho, dinh-ta-lien-phong-tho, dinhtalienphongtho	10	2023-09-14 00:29:02	2023-09-14 00:29:02
2c262dbc-d3ca-483e-b450-a19eacb472cd	87f37a55-07d9-43bd-9fca-7990aea56210	Núi Hàm Rồng	nui-ham-rong	Núi Hàm Rồng, nui ham rong, nui-ham-rong, nuihamrong	10	2023-09-14 00:29:02	2023-09-14 00:29:02
bfbd6dc3-08e2-432e-93d1-9ebc49650288	87f37a55-07d9-43bd-9fca-7990aea56210	Thành phố Sapa	thanh-pho-sapa	Thành phố Sapa, thanh pho sapa, thanh-pho-sapa, thanhphosapa	10	2023-09-14 00:29:02	2023-09-14 00:29:02
dfd0b171-a7f5-4656-865f-d3a847786f14	87f37a55-07d9-43bd-9fca-7990aea56210	Đỉnh Phanxipang	dinh-phanxipang	Đỉnh Phanxipang, dinh phanxipang, dinh-phanxipang, dinhphanxipang	10	2023-09-14 00:29:02	2023-09-14 00:29:02
f8632797-fe0b-4b4c-9832-3dafe09fb23e	87f37a55-07d9-43bd-9fca-7990aea56210	Bản Tà Phìn	ban-ta-phin	Bản Tà Phìn, ban ta phin, ban-ta-phin, bantaphin	10	2023-09-14 00:29:02	2023-09-14 00:29:02
6fb77bfe-09a7-4bac-b9a2-c9cd2dcffeed	87f37a55-07d9-43bd-9fca-7990aea56210	Bản Cát Cát	ban-cat-cat	Bản Cát Cát, ban cat cat, ban-cat-cat, bancatcat	10	2023-09-14 00:29:03	2023-09-14 00:29:03
486df355-4b79-4368-a6de-45ed2295cf5d	87f37a55-07d9-43bd-9fca-7990aea56210	Swing Sapa	swing-sapa	Swing Sapa, swing sapa, swing-sapa, swingsapa	10	2023-09-14 00:29:03	2023-09-14 00:29:03
ee47c5ac-e831-48bb-b879-e757698b9b70	87f37a55-07d9-43bd-9fca-7990aea56210	Thung Lũng Mường Hoa	thung-lung-muong-hoa	Thung Lũng Mường Hoa, thung lung muong hoa, thung-lung-muong-hoa, thunglungmuonghoa	10	2023-09-14 00:29:03	2023-09-14 00:29:03
bdf28f17-1441-4bb6-b1ff-18b972e7f00a	87f37a55-07d9-43bd-9fca-7990aea56210	Thác Bạc	thac-bac	Thác Bạc, thac bac, thac-bac, thacbac	10	2023-09-14 00:29:03	2023-09-14 00:29:03
96b24b24-b470-4b94-8b70-527bc3cf4072	87f37a55-07d9-43bd-9fca-7990aea56210	Cổng trời	cong-troi	Cổng trời, cong troi, cong-troi, congtroi	10	2023-09-14 00:29:03	2023-09-14 00:29:03
625f5aa2-8e59-4d71-b507-13f1b1b78654	87f37a55-07d9-43bd-9fca-7990aea56210	Nhà thờ đá Sapa	nha-tho-da-sapa	Nhà thờ đá Sapa, nha tho da sapa, nha-tho-da-sapa, nhathodasapa	10	2023-09-14 00:29:04	2023-09-14 00:29:04
9a44fbab-c0fc-4051-a3c4-8e2127d98097	87f37a55-07d9-43bd-9fca-7990aea56210	Cổng San	cong-san	Cổng San, cong san, cong-san, congsan	10	2023-09-14 00:29:04	2023-09-14 00:29:04
436fa655-0f93-4b88-a0b0-43dbfd79ac06	554ccc5a-cbb3-4a91-b61c-9e7d5bc8e168	Khu di tích chiến thắng Điện Biên Phủ	khu-di-tich-chien-thang-dien-bien-phu	Khu di tích chiến thắng Điện Biên Phủ, khu di tich chien thang dien bien phu, khu-di-tich-chien-thang-dien-bien-phu, khuditichchienthangdienbienphu	10	2023-09-14 00:29:04	2023-09-14 00:29:04
6548d711-a2e0-4153-a213-41cd84a6d300	554ccc5a-cbb3-4a91-b61c-9e7d5bc8e168	Thị xã Mường Thanh	thi-xa-muong-thanh	Thị xã Mường Thanh, thi xa muong thanh, thi-xa-muong-thanh, thixamuongthanh	10	2023-09-14 00:29:04	2023-09-14 00:29:04
a6e40076-f10f-462f-a165-3b7bac1e0eed	554ccc5a-cbb3-4a91-b61c-9e7d5bc8e168	Cầu Hang Tôm	cau-hang-tom	Cầu Hang Tôm, cau hang tom, cau-hang-tom, cauhangtom	10	2023-09-14 00:29:04	2023-09-14 00:29:04
77d37ef9-5d6a-40f3-8d80-d85d0670232c	554ccc5a-cbb3-4a91-b61c-9e7d5bc8e168	Đèo Pha Đin	deo-pha-din	Đèo Pha Đin, deo pha din, deo-pha-din, deophadin	10	2023-09-14 00:29:05	2023-09-14 00:29:05
c042dc5c-8ef3-459d-882e-d798c01c4fe4	554ccc5a-cbb3-4a91-b61c-9e7d5bc8e168	Hồ Pá Khoang	ho-pa-khoang	Hồ Pá Khoang, ho pa khoang, ho-pa-khoang, hopakhoang	10	2023-09-14 00:29:05	2023-09-14 00:29:05
2a514837-1dc4-4b9e-a872-16c0a2a2c5aa	554ccc5a-cbb3-4a91-b61c-9e7d5bc8e168	Suối khoáng nóng Hua Pe	suoi-khoang-nong-hua-pe	Suối khoáng nóng Hua Pe, suoi khoang nong hua pe, suoi-khoang-nong-hua-pe, suoikhoangnonghuape	10	2023-09-14 00:29:05	2023-09-14 00:29:05
db4aa999-4e27-4f3d-83b6-8200cc204cb2	554ccc5a-cbb3-4a91-b61c-9e7d5bc8e168	Cực Tây A Pa Chải	cuc-tay-a-pa-chai	Cực Tây A Pa Chải, cuc tay a pa chai, cuc-tay-a-pa-chai, cuctayapachai	10	2023-09-14 00:29:05	2023-09-14 00:29:05
5a7daea4-685b-479d-93ce-c73407c5c9ce	5f8fd043-51de-472a-8b07-1292c906ee0f	Mù Cang Chải	mu-cang-chai	Mù Cang Chải, mu cang chai, mu-cang-chai, mucangchai	10	2023-09-14 00:29:05	2023-09-14 00:29:05
cfdc7a34-b46e-4ba6-9452-620e161855cc	5f8fd043-51de-472a-8b07-1292c906ee0f	Xã La Pán Tẩn	xa-la-pan-tan	Xã La Pán Tẩn, xa la pan tan, xa-la-pan-tan, xalapantan	10	2023-09-14 00:29:06	2023-09-14 00:29:06
9f3e1c75-cf45-4efc-ae90-ebefcbb6b336	5f8fd043-51de-472a-8b07-1292c906ee0f	Suối Giàng	suoi-giang	Suối Giàng, suoi giang, suoi-giang, suoigiang	10	2023-09-14 00:29:06	2023-09-14 00:29:06
455432c3-841b-4d08-909d-138b6620b62f	5f8fd043-51de-472a-8b07-1292c906ee0f	Hồ Thác Bà	ho-thac-ba	Hồ Thác Bà, ho thac ba, ho-thac-ba, hothacba	10	2023-09-14 00:29:06	2023-09-14 00:29:06
88ff3871-adf6-4142-a023-7be44baecec4	5f8fd043-51de-472a-8b07-1292c906ee0f	Thác Pú Nhu	thac-pu-nhu	Thác Pú Nhu, thac pu nhu, thac-pu-nhu, thacpunhu	10	2023-09-14 00:29:06	2023-09-14 00:29:06
51c4c403-c2e1-47db-958f-d4603936c437	5f8fd043-51de-472a-8b07-1292c906ee0f	Xã Tú Lê	xa-tu-le	Xã Tú Lê, xa tu le, xa-tu-le, xatule	10	2023-09-14 00:29:06	2023-09-14 00:29:06
24500083-5c01-426e-b77d-24b018b7cf1c	5f8fd043-51de-472a-8b07-1292c906ee0f	Bản Lìm Mông	ban-lim-mong	Bản Lìm Mông, ban lim mong, ban-lim-mong, banlimmong	10	2023-09-14 00:29:07	2023-09-14 00:29:07
063d6555-073b-41b4-93e3-b8a251bf6580	5f8fd043-51de-472a-8b07-1292c906ee0f	Cánh đồng Mường Lò	canh-dong-muong-lo	Cánh đồng Mường Lò, canh dong muong lo, canh-dong-muong-lo, canhdongmuonglo	10	2023-09-14 00:29:07	2023-09-14 00:29:07
627f73d7-da97-432e-bf5f-71e1c4fe36ff	5f8fd043-51de-472a-8b07-1292c906ee0f	Hồ Chóp Dù	ho-chop-du	Hồ Chóp Dù, ho chop du, ho-chop-du, hochopdu	10	2023-09-14 00:29:07	2023-09-14 00:29:07
c6388815-9646-45db-bd00-14ef89ff3297	0681823c-0557-42b2-b8f4-9b3ff5a583fe	Hồ Chiềng Khoi	ho-chieng-khoi	Hồ Chiềng Khoi, ho chieng khoi, ho-chieng-khoi, hochiengkhoi	10	2023-09-14 00:29:07	2023-09-14 00:29:07
2274005b-90dc-4591-a81b-125d27a4ed74	0681823c-0557-42b2-b8f4-9b3ff5a583fe	Bản Mòng	ban-mong	Bản Mòng, ban mong, ban-mong, banmong	10	2023-09-14 00:29:07	2023-09-14 00:29:07
5cc4a1b0-0050-419b-9610-f5420fa2cf65	0681823c-0557-42b2-b8f4-9b3ff5a583fe	Thác Dải Yếm	thac-dai-yem	Thác Dải Yếm, thac dai yem, thac-dai-yem, thacdaiyem	10	2023-09-14 00:29:08	2023-09-14 00:29:08
82e7e1f2-8446-4c52-a122-a61157233625	0681823c-0557-42b2-b8f4-9b3ff5a583fe	Hồ Tiền Phong	ho-tien-phong	Hồ Tiền Phong, ho tien phong, ho-tien-phong, hotienphong	10	2023-09-14 00:29:08	2023-09-14 00:29:08
9bcb222a-7122-4652-836d-ce1d52f3dd50	0681823c-0557-42b2-b8f4-9b3ff5a583fe	Núi Pha Luông	nui-pha-luong	Núi Pha Luông, nui pha luong, nui-pha-luong, nuiphaluong	10	2023-09-14 00:29:08	2023-09-14 00:29:08
acc95eaa-1480-4fec-97e5-a4d496af759c	0681823c-0557-42b2-b8f4-9b3ff5a583fe	Thành phố Sơn La	thanh-pho-son-la	Thành phố Sơn La, thanh pho son la, thanh-pho-son-la, thanhphosonla	10	2023-09-14 00:29:08	2023-09-14 00:29:08
923864dc-0348-4226-8e32-459e0f6b9f36	0681823c-0557-42b2-b8f4-9b3ff5a583fe	Nhà ngục Sơn La	nha-nguc-son-la	Nhà ngục Sơn La, nha nguc son la, nha-nguc-son-la, nhangucsonla	10	2023-09-14 00:29:08	2023-09-14 00:29:08
1ca5307e-6781-4479-9c8c-b6399c0e8312	0681823c-0557-42b2-b8f4-9b3ff5a583fe	Cụm du lịch Sông Đà	cum-du-lich-song-da	Cụm du lịch Sông Đà, cum du lich song da, cum-du-lich-song-da, cumdulichsongda	10	2023-09-14 00:29:09	2023-09-14 00:29:09
e88a2ce1-14e4-4881-ad36-96b8a40e4c54	0681823c-0557-42b2-b8f4-9b3ff5a583fe	Động Sơn Mộc Hương	dong-son-moc-huong	Động Sơn Mộc Hương, dong son moc huong, dong-son-moc-huong, dongsonmochuong	10	2023-09-14 00:29:09	2023-09-14 00:29:09
bc5aeac3-a67c-4821-8827-9342d616d497	0681823c-0557-42b2-b8f4-9b3ff5a583fe	Mộc Châu	moc-chau	Mộc Châu, moc chau, moc-chau, mocchau	10	2023-09-14 00:29:09	2023-09-14 00:29:09
c7cf162a-4698-45c1-b643-798803f58627	0681823c-0557-42b2-b8f4-9b3ff5a583fe	bảo tàng Sơn La	bao-tang-son-la	bảo tàng Sơn La, bao tang son la, bao-tang-son-la, baotangsonla	10	2023-09-14 00:29:09	2023-09-14 00:29:09
73572786-ded7-4093-a67d-5c9db5bf4150	35e46bd6-df18-43e6-ae61-7e2a650dbd2e	Lũng Vân – Nóc nhà xứ Mường	lung-van-noc-nha-xu-muong	Lũng Vân – Nóc nhà xứ Mường, lung van – noc nha xu muong, lung-van-noc-nha-xu-muong, lungvannocnhaxumuong	10	2023-09-14 00:29:09	2023-09-14 00:29:09
f80cfd42-768e-4c71-8e26-12f559fb3830	35e46bd6-df18-43e6-ae61-7e2a650dbd2e	Cửu thác Tú Sơn	cuu-thac-tu-son	Cửu thác Tú Sơn, cuu thac tu son, cuu-thac-tu-son, cuuthactuson	10	2023-09-14 00:29:10	2023-09-14 00:29:10
e2e1ee28-4b88-473b-b5f9-7c85d8b4618c	35e46bd6-df18-43e6-ae61-7e2a650dbd2e	Nhà máy thủy điện Hòa Bình	nha-may-thuy-dien-hoa-binh	Nhà máy thủy điện Hòa Bình, nha may thuy dien hoa binh, nha-may-thuy-dien-hoa-binh, nhamaythuydienhoabinh	10	2023-09-14 00:29:10	2023-09-14 00:29:10
f06cb06c-b2be-45ab-9ad5-13ab18f01245	35e46bd6-df18-43e6-ae61-7e2a650dbd2e	Thung Nai – Hạ Long trên cạn	thung-nai-ha-long-tren-can	Thung Nai – Hạ Long trên cạn, thung nai – ha long tren can, thung-nai-ha-long-tren-can, thungnaihalongtrencan	10	2023-09-14 00:29:10	2023-09-14 00:29:10
f3973a10-8b2a-4f6d-9b50-14af62c2c626	35e46bd6-df18-43e6-ae61-7e2a650dbd2e	Mai Châu – Vùng đất thơ mộng	mai-chau-vung-dat-tho-mong	Mai Châu – Vùng đất thơ mộng, mai chau – vung dat tho mong, mai-chau-vung-dat-tho-mong, maichauvungdatthomong	10	2023-09-14 00:29:10	2023-09-14 00:29:10
06371c68-7e17-4d7f-aaa9-6d5068179e94	35e46bd6-df18-43e6-ae61-7e2a650dbd2e	Động Thác Đá	dong-thac-da	Động Thác Đá, dong thac da, dong-thac-da, dongthacda	10	2023-09-14 00:29:10	2023-09-14 00:29:10
3aa41610-8a2b-405f-a9f7-56fc2b553ae2	35e46bd6-df18-43e6-ae61-7e2a650dbd2e	Khu du lịch thác Thăng Thiên	khu-du-lich-thac-thang-thien	Khu du lịch thác Thăng Thiên, khu du lich thac thang thien, khu-du-lich-thac-thang-thien, khudulichthacthangthien	10	2023-09-14 00:29:11	2023-09-14 00:29:11
8ecacc1d-72db-4025-b524-790813d00803	35e46bd6-df18-43e6-ae61-7e2a650dbd2e	Động Hoa Tiên	dong-hoa-tien	Động Hoa Tiên, dong hoa tien, dong-hoa-tien, donghoatien	10	2023-09-14 00:29:11	2023-09-14 00:29:11
9af0f8ff-c26a-4b19-9f07-ed8092aed395	90379627-06fa-4e12-b835-9991b3bc96c7	Vũng Rô	vung-ro	Vũng Rô, vung ro, vung-ro, vungro	10	2023-09-14 00:29:50	2023-09-14 00:29:50
151ed708-33a8-4fb2-bc35-0b4b17e172e6	35e46bd6-df18-43e6-ae61-7e2a650dbd2e	Động Thác Bờ	dong-thac-bo	Động Thác Bờ, dong thac bo, dong-thac-bo, dongthacbo	10	2023-09-14 00:29:11	2023-09-14 00:29:11
6e6da466-ce39-4125-8563-224fd78473f1	04d4ba96-d58d-44ff-9cbe-d7bb28cfc244	Làng cổ đường Lâm	lang-co-duong-lam	Làng cổ đường Lâm, lang co duong lam, lang-co-duong-lam, langcoduonglam	10	2023-09-14 00:29:11	2023-09-14 00:29:11
0b16baa6-c10b-4ac3-b85c-a030a0f05128	04d4ba96-d58d-44ff-9cbe-d7bb28cfc244	Hồ Quan Sơn	ho-quan-son	Hồ Quan Sơn, ho quan son, ho-quan-son, hoquanson	10	2023-09-14 00:29:11	2023-09-14 00:29:11
483275ac-9c9a-43cc-8c31-1799c07b441b	04d4ba96-d58d-44ff-9cbe-d7bb28cfc244	Khu du lịch Đồng Mô	khu-du-lich-dong-mo	Khu du lịch Đồng Mô, khu du lich dong mo, khu-du-lich-dong-mo, khudulichdongmo	10	2023-09-14 00:29:12	2023-09-14 00:29:12
cde2cc09-d1b7-4dff-a62c-a723353ff79a	04d4ba96-d58d-44ff-9cbe-d7bb28cfc244	Khu sinh thái Chùa Trầm	khu-sinh-thai-chua-tram	Khu sinh thái Chùa Trầm, khu sinh thai chua tram, khu-sinh-thai-chua-tram, khusinhthaichuatram	10	2023-09-14 00:29:12	2023-09-14 00:29:12
103079a4-778f-4a61-99be-a6fc8228ddb8	04d4ba96-d58d-44ff-9cbe-d7bb28cfc244	Hàm Lợn	ham-lon	Hàm Lợn, ham lon, ham-lon, hamlon	10	2023-09-14 00:29:12	2023-09-14 00:29:12
45e716b3-0354-4efb-a1fd-d6de06c6d002	04d4ba96-d58d-44ff-9cbe-d7bb28cfc244	Khu sinh thái Ba Vì	khu-sinh-thai-ba-vi	Khu sinh thái Ba Vì, khu sinh thai ba vi, khu-sinh-thai-ba-vi, khusinhthaibavi	10	2023-09-14 00:29:12	2023-09-14 00:29:12
6f913beb-a71d-47c6-a11e-82256ffe3c11	04d4ba96-d58d-44ff-9cbe-d7bb28cfc244	My Hill Sóc Sơn	my-hill-soc-son	My Hill Sóc Sơn, my hill soc son, my-hill-soc-son, myhillsocson	10	2023-09-14 00:29:12	2023-09-14 00:29:12
78618306-ea0b-439c-a93f-257127ace3c4	04d4ba96-d58d-44ff-9cbe-d7bb28cfc244	Đền gióng – Sóc sơn	den-giong-soc-son	Đền gióng – Sóc sơn, den giong – soc son, den-giong-soc-son, dengiongsocson	10	2023-09-14 00:29:12	2023-09-14 00:29:12
1eff55c8-13ee-44e2-960b-7c0eacd83e9f	04d4ba96-d58d-44ff-9cbe-d7bb28cfc244	Làng văn hóa 54 dân tộc 	lang-van-hoa-54-dan-toc	Làng văn hóa 54 dân tộc , lang van hoa 54 dan toc , lang-van-hoa-54-dan-toc, langvanhoa54dantoc	10	2023-09-14 00:29:13	2023-09-14 00:29:13
0c6527e9-8080-421f-bb67-f757018c8d1f	7fd0ed8f-97b7-414d-b2d2-d87a9369f09d	Hồ Đại Lải Vĩnh Phúc	ho-dai-lai-vinh-phuc	Hồ Đại Lải Vĩnh Phúc, ho dai lai vinh phuc, ho-dai-lai-vinh-phuc, hodailaivinhphuc	10	2023-09-14 00:29:13	2023-09-14 00:29:13
ced806d5-7a69-4c47-b195-be5c12dd326b	7fd0ed8f-97b7-414d-b2d2-d87a9369f09d	Làng hoa Mê Linh	lang-hoa-me-linh	Làng hoa Mê Linh, lang hoa me linh, lang-hoa-me-linh, langhoamelinh	10	2023-09-14 00:29:13	2023-09-14 00:29:13
41e49fd7-b346-4d2c-b9c4-a036c22806c3	7fd0ed8f-97b7-414d-b2d2-d87a9369f09d	Tháp Bình Sơn	thap-binh-son	Tháp Bình Sơn, thap binh son, thap-binh-son, thapbinhson	10	2023-09-14 00:29:13	2023-09-14 00:29:13
2b6ea7f9-e8da-4b6b-86c1-792e36dbed34	7fd0ed8f-97b7-414d-b2d2-d87a9369f09d	Cụm đình Tam Canh	cum-dinh-tam-canh	Cụm đình Tam Canh, cum dinh tam canh, cum-dinh-tam-canh, cumdinhtamcanh	10	2023-09-14 00:29:13	2023-09-14 00:29:13
12b102e4-4e06-4bf0-9029-45466d31d55e	7fd0ed8f-97b7-414d-b2d2-d87a9369f09d	Làng gốm Hương Canh	lang-gom-huong-canh	Làng gốm Hương Canh, lang gom huong canh, lang-gom-huong-canh, langgomhuongcanh	10	2023-09-14 00:29:14	2023-09-14 00:29:14
c4f8e9ab-7d9e-46c8-b135-24550b16b3a7	7fd0ed8f-97b7-414d-b2d2-d87a9369f09d	Hồ Xạ Hương	ho-xa-huong	Hồ Xạ Hương, ho xa huong, ho-xa-huong, hoxahuong	10	2023-09-14 00:29:14	2023-09-14 00:29:14
f5fd2445-2fdb-449d-929a-11c103c88be9	7fd0ed8f-97b7-414d-b2d2-d87a9369f09d	Vườn quốc gia Tam Đảo	vuon-quoc-gia-tam-dao	Vườn quốc gia Tam Đảo, vuon quoc gia tam dao, vuon-quoc-gia-tam-dao, vuonquocgiatamdao	10	2023-09-14 00:29:14	2023-09-14 00:29:14
7c8e4e18-2d61-441e-8fc8-3d3d580484db	7fd0ed8f-97b7-414d-b2d2-d87a9369f09d	Chùa Tây Thiên Vĩnh Phúc	chua-tay-thien-vinh-phuc	Chùa Tây Thiên Vĩnh Phúc, chua tay thien vinh phuc, chua-tay-thien-vinh-phuc, chuataythienvinhphuc	10	2023-09-14 00:29:14	2023-09-14 00:29:14
57851543-ec15-4a87-bc1c-c483a68e4ade	9569499d-637b-4e44-ac7e-31107ae665ef	Chùa Phật Tích	chua-phat-tich	Chùa Phật Tích, chua phat tich, chua-phat-tich, chuaphattich	10	2023-09-14 00:29:15	2023-09-14 00:29:15
b3e71860-2bc6-49d5-839d-b15056970a9f	9569499d-637b-4e44-ac7e-31107ae665ef	Đền Đô	den-do	Đền Đô, den do, den-do, dendo	10	2023-09-14 00:29:15	2023-09-14 00:29:15
f30c58b7-427e-460e-8b65-be58dc3d9404	9569499d-637b-4e44-ac7e-31107ae665ef	Hội Lim	hoi-lim	Hội Lim, hoi lim, hoi-lim, hoilim	10	2023-09-14 00:29:15	2023-09-14 00:29:15
dc1aa939-e292-4fd6-b979-dc8172907153	9569499d-637b-4e44-ac7e-31107ae665ef	Đình Đình Bảng	dinh-dinh-bang	Đình Đình Bảng, dinh dinh bang, dinh-dinh-bang, dinhdinhbang	10	2023-09-14 00:29:15	2023-09-14 00:29:15
bf2289fd-6b50-4b83-91f8-1d861a8d0370	9569499d-637b-4e44-ac7e-31107ae665ef	Chùa Dâu	chua-dau	Chùa Dâu, chua dau, chua-dau, chuadau	10	2023-09-14 00:29:15	2023-09-14 00:29:15
1639b515-659e-490b-ab22-ae9ac9cd936f	9569499d-637b-4e44-ac7e-31107ae665ef	Chùa Bút Tháp	chua-but-thap	Chùa Bút Tháp, chua but thap, chua-but-thap, chuabutthap	10	2023-09-14 00:29:16	2023-09-14 00:29:16
9f0fd574-8c37-41db-96b5-0c5fb855f85f	9569499d-637b-4e44-ac7e-31107ae665ef	Đền Bà chúa Kho	den-ba-chua-kho	Đền Bà chúa Kho, den ba chua kho, den-ba-chua-kho, denbachuakho	10	2023-09-14 00:29:16	2023-09-14 00:29:16
123a1db9-c495-4d96-94c0-eae10c4a69c0	9569499d-637b-4e44-ac7e-31107ae665ef	Làng Đình Bảng	lang-dinh-bang	Làng Đình Bảng, lang dinh bang, lang-dinh-bang, langdinhbang	10	2023-09-14 00:29:16	2023-09-14 00:29:16
3a0a9187-09c2-4be6-9526-ea27c7221b27	9569499d-637b-4e44-ac7e-31107ae665ef	Làng Tranh Đông Hồ	lang-tranh-dong-ho	Làng Tranh Đông Hồ, lang tranh dong ho, lang-tranh-dong-ho, langtranhdongho	10	2023-09-14 00:29:16	2023-09-14 00:29:16
af673438-72cf-49b3-9d69-d2dfe8beb7fe	9569499d-637b-4e44-ac7e-31107ae665ef	Làng gốm Phù Lãng	lang-gom-phu-lang	Làng gốm Phù Lãng, lang gom phu lang, lang-gom-phu-lang, langgomphulang	10	2023-09-14 00:29:16	2023-09-14 00:29:16
803cf314-07a3-43be-8aaa-e8be9a9ce3ae	7a13bf0c-f16f-416e-b5a0-d24fd16ebc18	Đền Mẫu	den-mau	Đền Mẫu, den mau, den-mau, denmau	10	2023-09-14 00:29:17	2023-09-14 00:29:17
5d470ed2-e026-4827-a130-541de357e3db	7a13bf0c-f16f-416e-b5a0-d24fd16ebc18	Chùa Hiến	chua-hien	Chùa Hiến, chua hien, chua-hien, chuahien	10	2023-09-14 00:29:17	2023-09-14 00:29:17
a6021800-ac6c-4355-8296-8808412542c0	7a13bf0c-f16f-416e-b5a0-d24fd16ebc18	Văn Miếu Xích Đằng	van-mieu-xich-dang	Văn Miếu Xích Đằng, van mieu xich dang, van-mieu-xich-dang, vanmieuxichdang	10	2023-09-14 00:29:17	2023-09-14 00:29:17
8f8249c9-9799-4838-a142-fd4c7e957c25	7a13bf0c-f16f-416e-b5a0-d24fd16ebc18	Phố Nối	pho-noi	Phố Nối, pho noi, pho-noi, phonoi	10	2023-09-14 00:29:17	2023-09-14 00:29:17
2227b47b-c3af-487e-bea0-a05232d4e604	7a13bf0c-f16f-416e-b5a0-d24fd16ebc18	Đền Chử Đồng Tử	den-chu-dong-tu	Đền Chử Đồng Tử, den chu dong tu, den-chu-dong-tu, denchudongtu	10	2023-09-14 00:29:17	2023-09-14 00:29:17
1f8fc362-20c6-4a14-b351-f312a78dfc33	8bd99eab-9f6d-4a2b-b7d4-560a90dd775d	Bãi biển Đồng Châu	bai-bien-dong-chau	Bãi biển Đồng Châu, bai bien dong chau, bai-bien-dong-chau, baibiendongchau	10	2023-09-14 00:29:18	2023-09-14 00:29:18
d7764b76-a343-4d0d-9b13-797c14fc9440	04d4ba96-d58d-44ff-9cbe-d7bb28cfc244	Chùa Một Cột	chua-mot-cot	Chùa Một Cột, chua mot cot, chua-mot-cot, chuamotcot	10	2023-09-14 00:29:37	2023-09-14 00:29:37
d21e0728-0446-4430-a540-e0c83056c995	7a13bf0c-f16f-416e-b5a0-d24fd16ebc18	Cụm di tích Đa Hòa – Dạ Trạch	cum-di-tich-da-hoa-da-trach	Cụm di tích Đa Hòa – Dạ Trạch, cum di tich da hoa – da trach, cum-di-tich-da-hoa-da-trach, cumditichdahoadatrach	10	2023-09-14 00:29:18	2023-09-14 00:29:18
35a95881-f421-4316-a1d0-d40ae97c0401	7a13bf0c-f16f-416e-b5a0-d24fd16ebc18	Hàm Tử – Bãi Sậy, Phố Hiến	ham-tu-bai-say-pho-hien	Hàm Tử – Bãi Sậy, Phố Hiến, ham tu – bai say, pho hien, ham-tu-bai-say-pho-hien, hamtubaisayphohien	10	2023-09-14 00:29:18	2023-09-14 00:29:18
bb768ebc-babc-4ea7-b3ca-3f9d1303210a	7a13bf0c-f16f-416e-b5a0-d24fd16ebc18	Làng Nôm	lang-nom	Làng Nôm, lang nom, lang-nom, langnom	10	2023-09-14 00:29:18	2023-09-14 00:29:18
2643a03c-12cf-4c30-be90-a0f87a58a9a6	7a13bf0c-f16f-416e-b5a0-d24fd16ebc18	Đền Phương Hoàng	den-phuong-hoang	Đền Phương Hoàng, den phuong hoang, den-phuong-hoang, denphuonghoang	10	2023-09-14 00:29:18	2023-09-14 00:29:18
502f7cf8-015e-4cc2-953e-2d5008ddfff9	7a13bf0c-f16f-416e-b5a0-d24fd16ebc18	Hồ Bán Nguyệt	ho-ban-nguyet	Hồ Bán Nguyệt, ho ban nguyet, ho-ban-nguyet, hobannguyet	10	2023-09-14 00:29:19	2023-09-14 00:29:19
d4497730-3a74-4081-b4e5-d95c59bf6d28	633969d0-05ea-45a9-bf97-83bdca0921a1	Đình đá Tiên Phong	dinh-da-tien-phong	Đình đá Tiên Phong, dinh da tien phong, dinh-da-tien-phong, dinhdatienphong	10	2023-09-14 00:29:19	2023-09-14 00:29:19
8b919972-c847-4194-9a90-91d8957e1929	633969d0-05ea-45a9-bf97-83bdca0921a1	Đền Lãnh Giang	den-lanh-giang	Đền Lãnh Giang, den lanh giang, den-lanh-giang, denlanhgiang	10	2023-09-14 00:29:19	2023-09-14 00:29:19
32d9c01f-caeb-471a-b3bf-a4c765c7b0d6	633969d0-05ea-45a9-bf97-83bdca0921a1	Đền Trần Thương	den-tran-thuong	Đền Trần Thương, den tran thuong, den-tran-thuong, dentranthuong	10	2023-09-14 00:29:19	2023-09-14 00:29:19
7ce4e9aa-4e20-40aa-9e8d-c549a021864e	633969d0-05ea-45a9-bf97-83bdca0921a1	Đền Trúc	den-truc	Đền Trúc, den truc, den-truc, dentruc	10	2023-09-14 00:29:20	2023-09-14 00:29:20
d855f624-4293-4ce5-9500-05556b14001d	633969d0-05ea-45a9-bf97-83bdca0921a1	Đền Vũ Điện	den-vu-dien	Đền Vũ Điện, den vu dien, den-vu-dien, denvudien	10	2023-09-14 00:29:20	2023-09-14 00:29:20
2276925b-1793-424c-854f-6eb2c742b41d	633969d0-05ea-45a9-bf97-83bdca0921a1	Đền Lăng	den-lang	Đền Lăng, den lang, den-lang, denlang	10	2023-09-14 00:29:20	2023-09-14 00:29:20
7b32981a-5998-46d2-aeb4-8b3619755f0f	633969d0-05ea-45a9-bf97-83bdca0921a1	Kẽm Trống	kem-trong	Kẽm Trống, kem trong, kem-trong, kemtrong	10	2023-09-14 00:29:20	2023-09-14 00:29:20
4ac49b0e-6fe6-4401-8fc9-f85644a2b466	633969d0-05ea-45a9-bf97-83bdca0921a1	Bát cảnh sơn	bat-canh-son	Bát cảnh sơn, bat canh son, bat-canh-son, batcanhson	10	2023-09-14 00:29:20	2023-09-14 00:29:20
fc557914-e705-4b68-9931-54b25529a98a	633969d0-05ea-45a9-bf97-83bdca0921a1	Khu di tích văn hóa lịch sử	khu-di-tich-van-hoa-lich-su	Khu di tích văn hóa lịch sử, khu di tich van hoa lich su, khu-di-tich-van-hoa-lich-su, khuditichvanhoalichsu	10	2023-09-14 00:29:20	2023-09-14 00:29:20
956f6062-3cb4-46ee-bc04-9cca81f454b3	633969d0-05ea-45a9-bf97-83bdca0921a1	Chùa Bà Đanh	chua-ba-danh	Chùa Bà Đanh, chua ba danh, chua-ba-danh, chuabadanh	10	2023-09-14 00:29:21	2023-09-14 00:29:21
9b5f3034-4707-446b-b94e-8209d4dfd3ea	633969d0-05ea-45a9-bf97-83bdca0921a1	Chùa Tam Trúc	chua-tam-truc	Chùa Tam Trúc, chua tam truc, chua-tam-truc, chuatamtruc	10	2023-09-14 00:29:21	2023-09-14 00:29:21
af12f3dd-a415-41df-b6a6-d6478c4016f9	633969d0-05ea-45a9-bf97-83bdca0921a1	Chùa Phật Quang	chua-phat-quang	Chùa Phật Quang, chua phat quang, chua-phat-quang, chuaphatquang	10	2023-09-14 00:29:21	2023-09-14 00:29:21
94ba6e87-d27b-43ca-9937-8eee7a40fadb	633969d0-05ea-45a9-bf97-83bdca0921a1	Chùa Địa Tạng – Phi Lai Tự	chua-dia-tang-phi-lai-tu	Chùa Địa Tạng – Phi Lai Tự, chua dia tang – phi lai tu, chua-dia-tang-phi-lai-tu, chuadiatangphilaitu	10	2023-09-14 00:29:21	2023-09-14 00:29:21
17f1ad79-3427-44bb-be62-2b49beceefa9	5f712f0a-4bf4-4f17-8c6e-3fa1d4efe23a	Đền Kiếp Bạc	den-kiep-bac	Đền Kiếp Bạc, den kiep bac, den-kiep-bac, denkiepbac	10	2023-09-14 00:29:21	2023-09-14 00:29:21
422e84df-816c-434d-b7a2-5424ddd79df2	5f712f0a-4bf4-4f17-8c6e-3fa1d4efe23a	Đền Cao An Phụ	den-cao-an-phu	Đền Cao An Phụ, den cao an phu, den-cao-an-phu, dencaoanphu	10	2023-09-14 00:29:22	2023-09-14 00:29:22
1f420e61-120e-4274-a594-4e83d60940b8	5f712f0a-4bf4-4f17-8c6e-3fa1d4efe23a	Đảo Cò Lăng Nam	dao-co-lang-nam	Đảo Cò Lăng Nam, dao co lang nam, dao-co-lang-nam, daocolangnam	10	2023-09-14 00:29:22	2023-09-14 00:29:22
4b01e5a9-a834-406a-84f7-6bb216b27ab0	5f712f0a-4bf4-4f17-8c6e-3fa1d4efe23a	Chùa Côn Sơn	chua-con-son	Chùa Côn Sơn, chua con son, chua-con-son, chuaconson	10	2023-09-14 00:29:22	2023-09-14 00:29:22
98785037-d7fe-4a66-9da7-4431ccb3a578	5f712f0a-4bf4-4f17-8c6e-3fa1d4efe23a	Giếng Ngọc	gieng-ngoc	Giếng Ngọc, gieng ngoc, gieng-ngoc, giengngoc	10	2023-09-14 00:29:22	2023-09-14 00:29:22
9032c4e1-4d30-41db-a913-ba85c881d3b9	5f712f0a-4bf4-4f17-8c6e-3fa1d4efe23a	Bàn Cờ Tiên	ban-co-tien	Bàn Cờ Tiên, ban co tien, ban-co-tien, bancotien	10	2023-09-14 00:29:22	2023-09-14 00:29:22
5ba62c20-780e-4369-91f0-b5013bcd00e6	5f712f0a-4bf4-4f17-8c6e-3fa1d4efe23a	Chùa – Động Kính Chủ	chua-dong-kinh-chu	Chùa – Động Kính Chủ, chua – dong kinh chu, chua-dong-kinh-chu, chuadongkinhchu	10	2023-09-14 00:29:23	2023-09-14 00:29:23
26125885-4394-47df-980d-8cb048c6693a	5f712f0a-4bf4-4f17-8c6e-3fa1d4efe23a	Làng rối nước Thanh Hà	lang-roi-nuoc-thanh-ha	Làng rối nước Thanh Hà, lang roi nuoc thanh ha, lang-roi-nuoc-thanh-ha, langroinuocthanhha	10	2023-09-14 00:29:23	2023-09-14 00:29:23
af9caa28-0445-4cd7-aa88-e11543d5d9ec	5f712f0a-4bf4-4f17-8c6e-3fa1d4efe23a	Khu di tích Côn Sơn – Kiếp Bạc	khu-di-tich-con-son-kiep-bac	Khu di tích Côn Sơn – Kiếp Bạc, khu di tich con son – kiep bac, khu-di-tich-con-son-kiep-bac, khuditichconsonkiepbac	10	2023-09-14 00:29:23	2023-09-14 00:29:23
4188a890-decb-49f2-8f75-ff6b62e8dc51	c7a6d823-be55-4995-b116-4d098f8f89fd	Cát Bà	cat-ba	Cát Bà, cat ba, cat-ba, catba	10	2023-09-14 00:29:23	2023-09-14 00:29:23
0ed95c5f-7598-40d0-b22f-9ddfbeee01c4	c7a6d823-be55-4995-b116-4d098f8f89fd	Vịnh Lan Hạ	vinh-lan-ha	Vịnh Lan Hạ, vinh lan ha, vinh-lan-ha, vinhlanha	10	2023-09-14 00:29:23	2023-09-14 00:29:23
35772b89-9a6c-47ce-b599-262bcb75ce42	c7a6d823-be55-4995-b116-4d098f8f89fd	Bãi biển Đồ Sơn	bai-bien-do-son	Bãi biển Đồ Sơn, bai bien do son, bai-bien-do-son, baibiendoson	10	2023-09-14 00:29:24	2023-09-14 00:29:24
f47a1c78-d7bf-484c-b588-a6b8947e7b65	c7a6d823-be55-4995-b116-4d098f8f89fd	Bến Nghiêng	ben-nghieng	Bến Nghiêng, ben nghieng, ben-nghieng, bennghieng	10	2023-09-14 00:29:24	2023-09-14 00:29:24
4700c307-4d51-4baf-a516-1a6e02966d73	c7a6d823-be55-4995-b116-4d098f8f89fd	Bến Tàu	ben-tau	Bến Tàu, ben tau, ben-tau, bentau	10	2023-09-14 00:29:24	2023-09-14 00:29:24
f118cc65-9231-48a0-8618-7db8a87b62a2	c7a6d823-be55-4995-b116-4d098f8f89fd	Hải Đăng Hòn Dấu	hai-dang-hon-dau	Hải Đăng Hòn Dấu, hai dang hon dau, hai-dang-hon-dau, haidanghondau	10	2023-09-14 00:29:24	2023-09-14 00:29:24
4b7f485c-85c0-4908-b6de-1e942b8efaa3	c7a6d823-be55-4995-b116-4d098f8f89fd	Núi Voi	nui-voi	Núi Voi, nui voi, nui-voi, nuivoi	10	2023-09-14 00:29:24	2023-09-14 00:29:24
f3514333-5f8a-4787-a758-3d428b48c2ce	c7a6d823-be55-4995-b116-4d098f8f89fd	Rừng Quốc Gia Cát Bà	rung-quoc-gia-cat-ba	Rừng Quốc Gia Cát Bà, rung quoc gia cat ba, rung-quoc-gia-cat-ba, rungquocgiacatba	10	2023-09-14 00:29:25	2023-09-14 00:29:25
e6594590-e438-4500-ba1c-b45ac56ba4b6	8bd99eab-9f6d-4a2b-b7d4-560a90dd775d	khu du lịch sinh thái Cồn Vành	khu-du-lich-sinh-thai-con-vanh	khu du lịch sinh thái Cồn Vành, khu du lich sinh thai con vanh, khu-du-lich-sinh-thai-con-vanh, khudulichsinhthaiconvanh	10	2023-09-14 00:29:25	2023-09-14 00:29:25
30c0193a-2b6d-4b82-8eae-6e4486878a72	8bd99eab-9f6d-4a2b-b7d4-560a90dd775d	khu du lịch sinh thái Cồn Đen	khu-du-lich-sinh-thai-con-den	khu du lịch sinh thái Cồn Đen, khu du lich sinh thai con den, khu-du-lich-sinh-thai-con-den, khudulichsinhthaiconden	10	2023-09-14 00:29:25	2023-09-14 00:29:25
7e122b4b-d167-4e46-bd59-e57d6478e313	8bd99eab-9f6d-4a2b-b7d4-560a90dd775d	Chùa Keo	chua-keo	Chùa Keo, chua keo, chua-keo, chuakeo	10	2023-09-14 00:29:25	2023-09-14 00:29:25
e5d2154a-44cf-449c-b75d-a807298c5989	8bd99eab-9f6d-4a2b-b7d4-560a90dd775d	Đền Tiên La, Đền Đồng Bằng	den-tien-la-den-dong-bang	Đền Tiên La, Đền Đồng Bằng, den tien la, den dong bang, den-tien-la-den-dong-bang, dentienladendongbang	10	2023-09-14 00:29:25	2023-09-14 00:29:25
d1820dd4-54c0-49bc-935b-044d3e278b74	8bd99eab-9f6d-4a2b-b7d4-560a90dd775d	Làng vườn Bách Thuận	lang-vuon-bach-thuan	Làng vườn Bách Thuận, lang vuon bach thuan, lang-vuon-bach-thuan, langvuonbachthuan	10	2023-09-14 00:29:26	2023-09-14 00:29:26
7fee97b6-b8a4-49ff-a306-037eb4ee278e	8bd99eab-9f6d-4a2b-b7d4-560a90dd775d	Làng nghề chạm bạc Đồng Xâm	lang-nghe-cham-bac-dong-xam	Làng nghề chạm bạc Đồng Xâm, lang nghe cham bac dong xam, lang-nghe-cham-bac-dong-xam, langnghechambacdongxam	10	2023-09-14 00:29:26	2023-09-14 00:29:26
c10aae29-f38a-4bc1-96ee-6011cf6b4556	8bd99eab-9f6d-4a2b-b7d4-560a90dd775d	Làng dệt khăn, dệt vải Phương La – Thái Phương	lang-det-khan-det-vai-phuong-la-thai-phuong	Làng dệt khăn, dệt vải Phương La – Thái Phương, lang det khan, det vai phuong la – thai phuong, lang-det-khan-det-vai-phuong-la-thai-phuong, langdetkhandetvaiphuonglathaiphuong	10	2023-09-14 00:29:26	2023-09-14 00:29:26
08e0ebbd-f194-4b42-bbfe-883ca580e14b	8bd99eab-9f6d-4a2b-b7d4-560a90dd775d	Làng dệt chiếu Hới	lang-det-chieu-hoi	Làng dệt chiếu Hới, lang det chieu hoi, lang-det-chieu-hoi, langdetchieuhoi	10	2023-09-14 00:29:26	2023-09-14 00:29:26
0d7363ea-cbed-4513-8e76-395a521c7ba4	43200db0-f624-418f-9ef0-c0fe3dd1625f	Biển Quất Lâm	bien-quat-lam	Biển Quất Lâm, bien quat lam, bien-quat-lam, bienquatlam	10	2023-09-14 00:29:26	2023-09-14 00:29:26
a02baa7e-22d4-4a58-b281-00f2522155b2	43200db0-f624-418f-9ef0-c0fe3dd1625f	Bãi tắm Thịnh Long	bai-tam-thinh-long	Bãi tắm Thịnh Long, bai tam thinh long, bai-tam-thinh-long, baitamthinhlong	10	2023-09-14 00:29:27	2023-09-14 00:29:27
8e728178-2d8b-49fa-af85-ca276ff74592	43200db0-f624-418f-9ef0-c0fe3dd1625f	Khu di tích Phủ Dầy	khu-di-tich-phu-day	Khu di tích Phủ Dầy, khu di tich phu day, khu-di-tich-phu-day, khuditichphuday	10	2023-09-14 00:29:27	2023-09-14 00:29:27
1182129f-d924-4ca7-bf39-e6cd7b4985b4	43200db0-f624-418f-9ef0-c0fe3dd1625f	Vườn quốc gia Xuân Thủy	vuon-quoc-gia-xuan-thuy	Vườn quốc gia Xuân Thủy, vuon quoc gia xuan thuy, vuon-quoc-gia-xuan-thuy, vuonquocgiaxuanthuy	10	2023-09-14 00:29:27	2023-09-14 00:29:27
b09f5c56-acfa-43a1-a7bd-06994975c6de	43200db0-f624-418f-9ef0-c0fe3dd1625f	Đền Trần.	den-tran	Đền Trần., den tran., den-tran, dentran	10	2023-09-14 00:29:27	2023-09-14 00:29:27
a220a1a4-348f-41da-b4cf-2c381c957336	0fd6d988-5a99-4f8e-ba1f-513bf3f66043	Khu du lịch Tam Cốc	khu-du-lich-tam-coc	Khu du lịch Tam Cốc, khu du lich tam coc, khu-du-lich-tam-coc, khudulichtamcoc	10	2023-09-14 00:29:27	2023-09-14 00:29:27
16878eb6-cd5b-4626-b750-d8db795e17cc	0fd6d988-5a99-4f8e-ba1f-513bf3f66043	Khu di tích cố đô Hoa Lư	khu-di-tich-co-do-hoa-lu	Khu di tích cố đô Hoa Lư, khu di tich co do hoa lu, khu-di-tich-co-do-hoa-lu, khuditichcodohoalu	10	2023-09-14 00:29:28	2023-09-14 00:29:28
35af4c8f-74c7-4d56-9ac6-4c53c268a2f2	0fd6d988-5a99-4f8e-ba1f-513bf3f66043	Nhà Thờ Phát Diện	nha-tho-phat-dien	Nhà Thờ Phát Diện, nha tho phat dien, nha-tho-phat-dien, nhathophatdien	10	2023-09-14 00:29:28	2023-09-14 00:29:28
7c648874-216b-486a-96d6-deb63712ad09	0fd6d988-5a99-4f8e-ba1f-513bf3f66043	Khu du lịch Tràng An	khu-du-lich-trang-an	Khu du lịch Tràng An, khu du lich trang an, khu-du-lich-trang-an, khudulichtrangan	10	2023-09-14 00:29:28	2023-09-14 00:29:28
ab1e5012-4936-4364-81f6-ca1833f02266	0fd6d988-5a99-4f8e-ba1f-513bf3f66043	Chùa Bái Đính	chua-bai-dinh	Chùa Bái Đính, chua bai dinh, chua-bai-dinh, chuabaidinh	10	2023-09-14 00:29:28	2023-09-14 00:29:28
033df349-a3ff-4b9a-9efa-84be73d8ea08	0fd6d988-5a99-4f8e-ba1f-513bf3f66043	Vườn quốc gia Cúc Phương	vuon-quoc-gia-cuc-phuong	Vườn quốc gia Cúc Phương, vuon quoc gia cuc phuong, vuon-quoc-gia-cuc-phuong, vuonquocgiacucphuong	10	2023-09-14 00:29:28	2023-09-14 00:29:28
08e2f52f-ecb4-4795-86d9-eb651730afc3	0fd6d988-5a99-4f8e-ba1f-513bf3f66043	Chùa Bích Động	chua-bich-dong	Chùa Bích Động, chua bich dong, chua-bich-dong, chuabichdong	10	2023-09-14 00:29:28	2023-09-14 00:29:28
3d775d92-a37f-4177-a19a-20206d1db82b	0fd6d988-5a99-4f8e-ba1f-513bf3f66043	Động Thiên Hà	dong-thien-ha	Động Thiên Hà, dong thien ha, dong-thien-ha, dongthienha	10	2023-09-14 00:29:29	2023-09-14 00:29:29
c32932e1-11c0-4067-9807-c2061e97d16f	0fd6d988-5a99-4f8e-ba1f-513bf3f66043	Khu bảo Tồn Thiên Nhiên Vân Long	khu-bao-ton-thien-nhien-van-long	Khu bảo Tồn Thiên Nhiên Vân Long, khu bao ton thien nhien van long, khu-bao-ton-thien-nhien-van-long, khubaotonthiennhienvanlong	10	2023-09-14 00:29:29	2023-09-14 00:29:29
a08ccce2-cd0c-4452-9304-c44ace543675	0fd6d988-5a99-4f8e-ba1f-513bf3f66043	Vườn chim Thung Nham	vuon-chim-thung-nham	Vườn chim Thung Nham, vuon chim thung nham, vuon-chim-thung-nham, vuonchimthungnham	10	2023-09-14 00:29:29	2023-09-14 00:29:29
31852156-2346-4747-8a14-73d11b0aeaba	c71168b4-90e2-42b0-8f3d-10eb5fd44550	Sầm Sơn	sam-son	Sầm Sơn, sam son, sam-son, samson	10	2023-09-14 00:29:29	2023-09-14 00:29:29
4f38ce0d-c900-4e2f-a89d-42322bf57605	c71168b4-90e2-42b0-8f3d-10eb5fd44550	Biển Hải Tiến	bien-hai-tien	Biển Hải Tiến, bien hai tien, bien-hai-tien, bienhaitien	10	2023-09-14 00:29:29	2023-09-14 00:29:29
4914465d-352e-4402-a363-cc87e33a8ca6	c71168b4-90e2-42b0-8f3d-10eb5fd44550	Vườn Quốc Gia Bến En	vuon-quoc-gia-ben-en	Vườn Quốc Gia Bến En, vuon quoc gia ben en, vuon-quoc-gia-ben-en, vuonquocgiabenen	10	2023-09-14 00:29:30	2023-09-14 00:29:30
c7c060b2-47df-4a79-a028-9356bd9ec468	c71168b4-90e2-42b0-8f3d-10eb5fd44550	Thác Mây	thac-may	Thác Mây, thac may, thac-may, thacmay	10	2023-09-14 00:29:30	2023-09-14 00:29:30
181cbdce-a91e-453a-aa93-f76c64f58b15	c71168b4-90e2-42b0-8f3d-10eb5fd44550	Kho Mường	kho-muong	Kho Mường, kho muong, kho-muong, khomuong	10	2023-09-14 00:29:30	2023-09-14 00:29:30
eab0fb42-f64b-4324-b644-6b985424f953	c71168b4-90e2-42b0-8f3d-10eb5fd44550	Suối Cá Thần	suoi-ca-than	Suối Cá Thần, suoi ca than, suoi-ca-than, suoicathan	10	2023-09-14 00:29:30	2023-09-14 00:29:30
d74e84db-cdb7-4f24-b7f8-4a2a51ba6a12	c71168b4-90e2-42b0-8f3d-10eb5fd44550	Thành Nhà Hồ	thanh-nha-ho	Thành Nhà Hồ, thanh nha ho, thanh-nha-ho, thanhnhaho	10	2023-09-14 00:29:30	2023-09-14 00:29:30
7e1bc9a8-8857-431d-9175-f542d42ee256	c71168b4-90e2-42b0-8f3d-10eb5fd44550	Hòn Trống Mái	hon-trong-mai	Hòn Trống Mái, hon trong mai, hon-trong-mai, hontrongmai	10	2023-09-14 00:29:31	2023-09-14 00:29:31
025fcae4-6684-4096-8b76-40645383fb78	04d4ba96-d58d-44ff-9cbe-d7bb28cfc244	Phố cổ Hà Nội	pho-co-ha-noi	Phố cổ Hà Nội, pho co ha noi, pho-co-ha-noi, phocohanoi	10	2023-09-14 00:29:31	2023-09-14 00:29:31
8faa761e-9372-4c86-9467-620b07220005	c71168b4-90e2-42b0-8f3d-10eb5fd44550	Khu bảo tồn Thiên Nhiên Pù Luông	khu-bao-ton-thien-nhien-pu-luong	Khu bảo tồn Thiên Nhiên Pù Luông, khu bao ton thien nhien pu luong, khu-bao-ton-thien-nhien-pu-luong, khubaotonthiennhienpuluong	10	2023-09-14 00:29:31	2023-09-14 00:29:31
1f78e965-e4db-4ac1-bc79-7e700d127a85	c71168b4-90e2-42b0-8f3d-10eb5fd44550	Động Từ Thức	dong-tu-thuc	Động Từ Thức, dong tu thuc, dong-tu-thuc, dongtuthuc	10	2023-09-14 00:29:31	2023-09-14 00:29:31
36695128-a39b-4acb-b0be-eef39bf34861	5aaa6a5e-e634-4a73-86a5-74b196484609	Bãi Biển Cửa Lò	bai-bien-cua-lo	Bãi Biển Cửa Lò, bai bien cua lo, bai-bien-cua-lo, baibiencualo	10	2023-09-14 00:29:31	2023-09-14 00:29:31
5a8a22a8-e4dd-4256-a196-62bd8f711acf	5aaa6a5e-e634-4a73-86a5-74b196484609	Bãi Lữ Nghệ An	bai-lu-nghe-an	Bãi Lữ Nghệ An, bai lu nghe an, bai-lu-nghe-an, bailunghean	10	2023-09-14 00:29:32	2023-09-14 00:29:32
254193b6-a032-4b7d-8c58-9fb43a0829a9	5aaa6a5e-e634-4a73-86a5-74b196484609	Hang Thẩm Ồm	hang-tham-om	Hang Thẩm Ồm, hang tham om, hang-tham-om, hangthamom	10	2023-09-14 00:29:32	2023-09-14 00:29:32
3db886c5-0921-49ea-875f-009b0a537661	5aaa6a5e-e634-4a73-86a5-74b196484609	Làng Sen	lang-sen	Làng Sen, lang sen, lang-sen, langsen	10	2023-09-14 00:29:32	2023-09-14 00:29:32
c4c2bf42-dc14-464e-97c6-19fa59568ab1	5aaa6a5e-e634-4a73-86a5-74b196484609	Làng Hoàng Trù	lang-hoang-tru	Làng Hoàng Trù, lang hoang tru, lang-hoang-tru, langhoangtru	10	2023-09-14 00:29:32	2023-09-14 00:29:32
132e199a-262b-4e8c-ad1f-982ef2a56075	5aaa6a5e-e634-4a73-86a5-74b196484609	Khu di tích lịch sử	khu-di-tich-lich-su	Khu di tích lịch sử, khu di tich lich su, khu-di-tich-lich-su, khuditichlichsu	10	2023-09-14 00:29:32	2023-09-14 00:29:32
80be7f8d-4009-4d3a-999d-dacad5b38ff3	5aaa6a5e-e634-4a73-86a5-74b196484609	Khu di tích lịch sử Truông Bồn	khu-di-tich-lich-su-truong-bon	Khu di tích lịch sử Truông Bồn, khu di tich lich su truong bon, khu-di-tich-lich-su-truong-bon, khuditichlichsutruongbon	10	2023-09-14 00:29:32	2023-09-14 00:29:32
49c8e774-5d1a-484f-84c8-a322c86b46a1	5aaa6a5e-e634-4a73-86a5-74b196484609	Thác Khe Kèm	thac-khe-kem	Thác Khe Kèm, thac khe kem, thac-khe-kem, thackhekem	10	2023-09-14 00:29:33	2023-09-14 00:29:33
499038d1-e717-43ca-ad42-d4493652f82d	e947d435-b016-4482-b04b-c37031bb9032	Bãi Biển Hoành Sơn	bai-bien-hoanh-son	Bãi Biển Hoành Sơn, bai bien hoanh son, bai-bien-hoanh-son, baibienhoanhson	10	2023-09-14 00:29:33	2023-09-14 00:29:33
147d8d45-a623-4649-920b-14661137fd2a	e947d435-b016-4482-b04b-c37031bb9032	Khu du lịch sinh thái Sơn Kim	khu-du-lich-sinh-thai-son-kim	Khu du lịch sinh thái Sơn Kim, khu du lich sinh thai son kim, khu-du-lich-sinh-thai-son-kim, khudulichsinhthaisonkim	10	2023-09-14 00:29:33	2023-09-14 00:29:33
e68e4988-2dd9-4b2e-9e67-40b074e1e991	e947d435-b016-4482-b04b-c37031bb9032	Khu du lịch sinh thái hồ Trại Tiểu	khu-du-lich-sinh-thai-ho-trai-tieu	Khu du lịch sinh thái hồ Trại Tiểu, khu du lich sinh thai ho trai tieu, khu-du-lich-sinh-thai-ho-trai-tieu, khudulichsinhthaihotraitieu	10	2023-09-14 00:29:33	2023-09-14 00:29:33
9ebf8056-8bed-4daa-851d-3e7da2021f19	e947d435-b016-4482-b04b-c37031bb9032	Biển Thiên Cầm	bien-thien-cam	Biển Thiên Cầm, bien thien cam, bien-thien-cam, bienthiencam	10	2023-09-14 00:29:33	2023-09-14 00:29:33
602f475b-227e-4103-b60f-d119fefaf5b5	e947d435-b016-4482-b04b-c37031bb9032	Núi Hồng Lĩnh	nui-hong-linh	Núi Hồng Lĩnh, nui hong linh, nui-hong-linh, nuihonglinh	10	2023-09-14 00:29:34	2023-09-14 00:29:34
93d49b32-920b-4218-8995-408f8b9a41d8	e947d435-b016-4482-b04b-c37031bb9032	Làng cá Cửa Nhượng	lang-ca-cua-nhuong	Làng cá Cửa Nhượng, lang ca cua nhuong, lang-ca-cua-nhuong, langcacuanhuong	10	2023-09-14 00:29:34	2023-09-14 00:29:34
44530039-f48e-4f98-9d5b-f9b7e6cbf9de	e947d435-b016-4482-b04b-c37031bb9032	Hồ Kẻ Gỗ	ho-ke-go	Hồ Kẻ Gỗ, ho ke go, ho-ke-go, hokego	10	2023-09-14 00:29:34	2023-09-14 00:29:34
f065c49e-b38d-45d3-a476-9c4492a1f98e	e947d435-b016-4482-b04b-c37031bb9032	Ngã ba Đồng Lộc	nga-ba-dong-loc	Ngã ba Đồng Lộc, nga ba dong loc, nga-ba-dong-loc, ngabadongloc	10	2023-09-14 00:29:34	2023-09-14 00:29:34
5e08b69f-6754-41c4-9a6b-bf8a3a47d81e	e947d435-b016-4482-b04b-c37031bb9032	Khu lưu niệm đại thi hào Nguyễn Du	khu-luu-niem-dai-thi-hao-nguyen-du	Khu lưu niệm đại thi hào Nguyễn Du, khu luu niem dai thi hao nguyen du, khu-luu-niem-dai-thi-hao-nguyen-du, khuluuniemdaithihaonguyendu	10	2023-09-14 00:29:34	2023-09-14 00:29:34
22d63285-4677-46d1-9ee2-7aa7da2241d3	e947d435-b016-4482-b04b-c37031bb9032	Chùa Hương Tích	chua-huong-tich	Chùa Hương Tích, chua huong tich, chua-huong-tich, chuahuongtich	10	2023-09-14 00:29:35	2023-09-14 00:29:35
f663000b-5526-4ac6-bc88-0fd7fed13ac7	a07bdd0b-9f77-4b63-87b3-76e674b0907d	Biển Nhật Lệ – thành phố Đồng Hới	bien-nhat-le-thanh-pho-dong-hoi	Biển Nhật Lệ – thành phố Đồng Hới, bien nhat le – thanh pho dong hoi, bien-nhat-le-thanh-pho-dong-hoi, biennhatlethanhphodonghoi	10	2023-09-14 00:29:35	2023-09-14 00:29:35
b9948e9f-c957-44d2-a856-9521be5ec264	a07bdd0b-9f77-4b63-87b3-76e674b0907d	Suối nước Mọong – Bố Trạch	suoi-nuoc-moong-bo-trach	Suối nước Mọong – Bố Trạch, suoi nuoc moong – bo trach, suoi-nuoc-moong-bo-trach, suoinuocmoongbotrach	10	2023-09-14 00:29:35	2023-09-14 00:29:35
ed9db51c-33c8-4c28-8e45-7c449749f95c	a07bdd0b-9f77-4b63-87b3-76e674b0907d	Vườn Quốc Gia Phong Nha – Kẻ Bàng	vuon-quoc-gia-phong-nha-ke-bang	Vườn Quốc Gia Phong Nha – Kẻ Bàng, vuon quoc gia phong nha – ke bang, vuon-quoc-gia-phong-nha-ke-bang, vuonquocgiaphongnhakebang	10	2023-09-14 00:29:35	2023-09-14 00:29:35
3766cfd6-3f34-4f29-89ec-92494e8d7fef	a07bdd0b-9f77-4b63-87b3-76e674b0907d	Suối Khoáng nóng Bang – Lệ Thủy	suoi-khoang-nong-bang-le-thuy	Suối Khoáng nóng Bang – Lệ Thủy, suoi khoang nong bang – le thuy, suoi-khoang-nong-bang-le-thuy, suoikhoangnongbanglethuy	10	2023-09-14 00:29:35	2023-09-14 00:29:35
0ef9ab96-9fde-4dc0-acf4-5e572317e648	a07bdd0b-9f77-4b63-87b3-76e674b0907d	Bãi Đá Nhảy – huyện Bố Trạch	bai-da-nhay-huyen-bo-trach	Bãi Đá Nhảy – huyện Bố Trạch, bai da nhay – huyen bo trach, bai-da-nhay-huyen-bo-trach, baidanhayhuyenbotrach	10	2023-09-14 00:29:36	2023-09-14 00:29:36
f5d254b8-5e14-4efc-b7f4-b73df9331421	a07bdd0b-9f77-4b63-87b3-76e674b0907d	Vũng Chùa – Đảo Yến	vung-chua-dao-yen	Vũng Chùa – Đảo Yến, vung chua – dao yen, vung-chua-dao-yen, vungchuadaoyen	10	2023-09-14 00:29:36	2023-09-14 00:29:36
708ee20e-4a1b-45ae-a1aa-decd87bc23f8	a07bdd0b-9f77-4b63-87b3-76e674b0907d	Hang động Sơn Đoòng	hang-dong-son-doong	Hang động Sơn Đoòng, hang dong son doong, hang-dong-son-doong, hangdongsondoong	10	2023-09-14 00:29:36	2023-09-14 00:29:36
3ce1f2d0-e35b-4100-a9d1-42c8a98ce2d0	04d4ba96-d58d-44ff-9cbe-d7bb28cfc244	Hồ Hoàn Kiếm	ho-hoan-kiem	Hồ Hoàn Kiếm, ho hoan kiem, ho-hoan-kiem, hohoankiem	10	2023-09-14 00:29:36	2023-09-14 00:29:36
8a49d66e-061b-438e-b877-7df0dc2b8f9a	04d4ba96-d58d-44ff-9cbe-d7bb28cfc244	Đền Ngọc Sơn	den-ngoc-son	Đền Ngọc Sơn, den ngoc son, den-ngoc-son, denngocson	10	2023-09-14 00:29:36	2023-09-14 00:29:36
911ebe86-c690-418b-8fc2-b740a4bd5798	04d4ba96-d58d-44ff-9cbe-d7bb28cfc244	Văn miếu Quốc Tử Giám	van-mieu-quoc-tu-giam	Văn miếu Quốc Tử Giám, van mieu quoc tu giam, van-mieu-quoc-tu-giam, vanmieuquoctugiam	10	2023-09-14 00:29:36	2023-09-14 00:29:36
e054d229-38b6-4475-8fac-4813d43dbbf5	04d4ba96-d58d-44ff-9cbe-d7bb28cfc244	Hồ Tây	ho-tay	Hồ Tây, ho tay, ho-tay, hotay	10	2023-09-14 00:29:37	2023-09-14 00:29:37
b97db94e-0ccd-4c75-a87a-ca54b7055335	04d4ba96-d58d-44ff-9cbe-d7bb28cfc244	Quảng Trường Ba Đình	quang-truong-ba-dinh	Quảng Trường Ba Đình, quang truong ba dinh, quang-truong-ba-dinh, quangtruongbadinh	10	2023-09-14 00:29:37	2023-09-14 00:29:37
f8b14489-66e6-435e-9cd5-579dafc4fc8b	04d4ba96-d58d-44ff-9cbe-d7bb28cfc244	Công viên Thủ Lệ	cong-vien-thu-le	Công viên Thủ Lệ, cong vien thu le, cong-vien-thu-le, congvienthule	10	2023-09-14 00:29:37	2023-09-14 00:29:37
6bc04ef6-6fb7-465a-a968-f9eb4e3b858c	04d4ba96-d58d-44ff-9cbe-d7bb28cfc244	Công viên Thống Nhất	cong-vien-thong-nhat	Công viên Thống Nhất, cong vien thong nhat, cong-vien-thong-nhat, congvienthongnhat	10	2023-09-14 00:29:37	2023-09-14 00:29:37
339ad22b-c149-4a20-bea9-9254549848e7	04d4ba96-d58d-44ff-9cbe-d7bb28cfc244	Royal City	royal-city	Royal City, royal city, royal-city, royalcity	10	2023-09-14 00:29:38	2023-09-14 00:29:38
ecf31cf0-62fb-438a-ba80-a4f4429f991d	04d4ba96-d58d-44ff-9cbe-d7bb28cfc244	Time City	time-city	Time City, time city, time-city, timecity	10	2023-09-14 00:29:38	2023-09-14 00:29:38
8f033093-f1f7-48ab-a679-ac10ac7afd20	04d4ba96-d58d-44ff-9cbe-d7bb28cfc244	Công viên nước Đầm Sen	cong-vien-nuoc-dam-sen	Công viên nước Đầm Sen, cong vien nuoc dam sen, cong-vien-nuoc-dam-sen, congviennuocdamsen	10	2023-09-14 00:29:38	2023-09-14 00:29:38
29ab0dfe-c682-4dd2-bbce-cbac336ff239	6a6e7a7f-9390-4d95-a893-62dbee4700ab	Thác Động Tà Puồng	thac-dong-ta-puong	Thác Động Tà Puồng, thac dong ta puong, thac-dong-ta-puong, thacdongtapuong	10	2023-09-14 00:29:38	2023-09-14 00:29:38
a9f172be-2c71-4273-8bad-424d54f4b6a6	6a6e7a7f-9390-4d95-a893-62dbee4700ab	Khu danh thắng Đakrông	khu-danh-thang-dakrong	Khu danh thắng Đakrông, khu danh thang dakrong, khu-danh-thang-dakrong, khudanhthangdakrong	10	2023-09-14 00:29:38	2023-09-14 00:29:38
c812b75e-37f5-4066-ab52-1968b6449a44	6a6e7a7f-9390-4d95-a893-62dbee4700ab	Đảo Cồn Cò	dao-con-co	Đảo Cồn Cò, dao con co, dao-con-co, daoconco	10	2023-09-14 00:29:39	2023-09-14 00:29:39
e506f02e-521d-424e-9bb3-c70561bdcf3f	6a6e7a7f-9390-4d95-a893-62dbee4700ab	Thác Chênh Vênh	thac-chenh-venh	Thác Chênh Vênh, thac chenh venh, thac-chenh-venh, thacchenhvenh	10	2023-09-14 00:29:39	2023-09-14 00:29:39
7a52d094-12cb-4183-82e7-48f4e72548e1	6a6e7a7f-9390-4d95-a893-62dbee4700ab	Động Prai	dong-prai	Động Prai, dong prai, dong-prai, dongprai	10	2023-09-14 00:29:39	2023-09-14 00:29:39
82ffe1fa-5c21-4e32-bfe1-fddab70bd5de	c1ed9437-473b-4627-a853-2f9f85f7e0cb	Biển Lăng Cô	bien-lang-co	Biển Lăng Cô, bien lang co, bien-lang-co, bienlangco	10	2023-09-14 00:29:39	2023-09-14 00:29:39
847b5799-77c6-439d-8e97-1a26b8313a05	c1ed9437-473b-4627-a853-2f9f85f7e0cb	Trường Quốc Học Huế	truong-quoc-hoc-hue	Trường Quốc Học Huế, truong quoc hoc hue, truong-quoc-hoc-hue, truongquochochue	10	2023-09-14 00:29:39	2023-09-14 00:29:39
5a38f458-9d87-4c9b-9a11-570efd283a63	c1ed9437-473b-4627-a853-2f9f85f7e0cb	Đại Nội Huế	dai-noi-hue	Đại Nội Huế, dai noi hue, dai-noi-hue, dainoihue	10	2023-09-14 00:29:40	2023-09-14 00:29:40
89eb4e7e-ddf6-4ed3-b7e2-6f066a4995a0	c1ed9437-473b-4627-a853-2f9f85f7e0cb	Các lăng tẩm ở Huế	cac-lang-tam-o-hue	Các lăng tẩm ở Huế, cac lang tam o hue, cac-lang-tam-o-hue, caclangtamohue	10	2023-09-14 00:29:40	2023-09-14 00:29:40
b53c6148-c0fb-4f4c-beee-2e2d89931c45	c1ed9437-473b-4627-a853-2f9f85f7e0cb	Hồ Thủy Tiên	ho-thuy-tien	Hồ Thủy Tiên, ho thuy tien, ho-thuy-tien, hothuytien	10	2023-09-14 00:29:40	2023-09-14 00:29:40
75d35a53-5286-4e00-ae1a-a65f47d17d96	c1ed9437-473b-4627-a853-2f9f85f7e0cb	Biển Thuận An	bien-thuan-an	Biển Thuận An, bien thuan an, bien-thuan-an, bienthuanan	10	2023-09-14 00:29:40	2023-09-14 00:29:40
2b209578-fb26-4191-a1e9-8d5dce7dc1db	c1ed9437-473b-4627-a853-2f9f85f7e0cb	Biển Cảnh Dương	bien-canh-duong	Biển Cảnh Dương, bien canh duong, bien-canh-duong, biencanhduong	10	2023-09-14 00:29:40	2023-09-14 00:29:40
1085185a-4640-4a2f-9449-b70a025321c4	c1ed9437-473b-4627-a853-2f9f85f7e0cb	Biển Hàm Rồng	bien-ham-rong	Biển Hàm Rồng, bien ham rong, bien-ham-rong, bienhamrong	10	2023-09-14 00:29:40	2023-09-14 00:29:40
56ef0182-06d6-43ba-85b9-1c91af6895ea	c1ed9437-473b-4627-a853-2f9f85f7e0cb	Chùa Thiên Mụ	chua-thien-mu	Chùa Thiên Mụ, chua thien mu, chua-thien-mu, chuathienmu	10	2023-09-14 00:29:41	2023-09-14 00:29:41
12fe35e1-7934-45df-ab23-4ad898743d69	c1ed9437-473b-4627-a853-2f9f85f7e0cb	Đầm Lập An	dam-lap-an	Đầm Lập An, dam lap an, dam-lap-an, damlapan	10	2023-09-14 00:29:41	2023-09-14 00:29:41
59c88883-ccf7-4283-857c-106cea0b231a	c1ed9437-473b-4627-a853-2f9f85f7e0cb	Núi Bạch Mã	nui-bach-ma	Núi Bạch Mã, nui bach ma, nui-bach-ma, nuibachma	10	2023-09-14 00:29:41	2023-09-14 00:29:41
34e020fd-f9c3-4fb3-a461-2fa311840280	c1ed9437-473b-4627-a853-2f9f85f7e0cb	Phá Tam Giang	pha-tam-giang	Phá Tam Giang, pha tam giang, pha-tam-giang, phatamgiang	10	2023-09-14 00:29:41	2023-09-14 00:29:41
eeb7fd67-ad72-4784-85f5-3716e992089f	e173c981-3ac8-4843-b970-3cd4b59d26b8	Bà Nà Hill 	ba-na-hill	Bà Nà Hill , ba na hill , ba-na-hill, banahill	10	2023-09-14 00:29:41	2023-09-14 00:29:41
504750c1-2b23-493e-acd9-52ea94ee534d	e173c981-3ac8-4843-b970-3cd4b59d26b8	Cầu Vàng	cau-vang	Cầu Vàng, cau vang, cau-vang, cauvang	10	2023-09-14 00:29:42	2023-09-14 00:29:42
74191833-6f8e-455f-9fdf-5866b39a27ff	e173c981-3ac8-4843-b970-3cd4b59d26b8	Ngũ Hành Sơn	ngu-hanh-son	Ngũ Hành Sơn, ngu hanh son, ngu-hanh-son, nguhanhson	10	2023-09-14 00:29:42	2023-09-14 00:29:42
dc48f84f-1562-4bf1-a777-25b5a8a2a875	e173c981-3ac8-4843-b970-3cd4b59d26b8	Bán đảo Sơn Trà	ban-dao-son-tra	Bán đảo Sơn Trà, ban dao son tra, ban-dao-son-tra, bandaosontra	10	2023-09-14 00:29:42	2023-09-14 00:29:42
64f632be-fdbf-4911-af4d-819e0a30456f	e173c981-3ac8-4843-b970-3cd4b59d26b8	Công viên Biển Đông	cong-vien-bien-dong	Công viên Biển Đông, cong vien bien dong, cong-vien-bien-dong, congvienbiendong	10	2023-09-14 00:29:42	2023-09-14 00:29:42
fe513e8e-75a0-44b7-82c3-dfc36bdb5028	e173c981-3ac8-4843-b970-3cd4b59d26b8	Suối khoáng nóng Thần Tài	suoi-khoang-nong-than-tai	Suối khoáng nóng Thần Tài, suoi khoang nong than tai, suoi-khoang-nong-than-tai, suoikhoangnongthantai	10	2023-09-14 00:29:42	2023-09-14 00:29:42
22aa4d03-ac1e-40e9-af33-65aff0051457	e173c981-3ac8-4843-b970-3cd4b59d26b8	Giếng trời	gieng-troi	Giếng trời, gieng troi, gieng-troi, giengtroi	10	2023-09-14 00:29:42	2023-09-14 00:29:42
aaf18d92-c6af-487d-9f9e-316704dd4929	e173c981-3ac8-4843-b970-3cd4b59d26b8	Cầu Tình Yêu	cau-tinh-yeu	Cầu Tình Yêu, cau tinh yeu, cau-tinh-yeu, cautinhyeu	10	2023-09-14 00:29:43	2023-09-14 00:29:43
7a5d07bf-1ebe-40c5-9d8d-b12fdadfed9d	e173c981-3ac8-4843-b970-3cd4b59d26b8	Làng hoa tình yêu	lang-hoa-tinh-yeu	Làng hoa tình yêu, lang hoa tinh yeu, lang-hoa-tinh-yeu, langhoatinhyeu	10	2023-09-14 00:29:43	2023-09-14 00:29:43
839388ce-0cdc-49ee-96b0-d5172fd2b1ba	e173c981-3ac8-4843-b970-3cd4b59d26b8	Công viên Châu Á Asia Park	cong-vien-chau-a-asia-park	Công viên Châu Á Asia Park, cong vien chau a asia park, cong-vien-chau-a-asia-park, congvienchauaasiapark	10	2023-09-14 00:29:43	2023-09-14 00:29:43
817638d2-ec3d-41f6-864f-f790b80bca1c	e173c981-3ac8-4843-b970-3cd4b59d26b8	Fantasy Park	fantasy-park	Fantasy Park, fantasy park, fantasy-park, fantasypark	10	2023-09-14 00:29:43	2023-09-14 00:29:43
6936f97c-3e00-4541-a808-d2b66959333c	e173c981-3ac8-4843-b970-3cd4b59d26b8	Bãi biển Mỹ Khê	bai-bien-my-khe	Bãi biển Mỹ Khê, bai bien my khe, bai-bien-my-khe, baibienmykhe	10	2023-09-14 00:29:43	2023-09-14 00:29:43
bad595a7-96c8-4b42-a464-c580cb3aac2e	e173c981-3ac8-4843-b970-3cd4b59d26b8	Bãi biển Xuân Thiều	bai-bien-xuan-thieu	Bãi biển Xuân Thiều, bai bien xuan thieu, bai-bien-xuan-thieu, baibienxuanthieu	10	2023-09-14 00:29:44	2023-09-14 00:29:44
ece3a395-ae29-454f-b118-41f1f2dbf34b	e173c981-3ac8-4843-b970-3cd4b59d26b8	Bãi biển Nam Ô	bai-bien-nam-o	Bãi biển Nam Ô, bai bien nam o, bai-bien-nam-o, baibiennamo	10	2023-09-14 00:29:44	2023-09-14 00:29:44
123038ab-0f4b-4a87-ad1b-934e4480c39a	e173c981-3ac8-4843-b970-3cd4b59d26b8	Bãi tắm Non Nước	bai-tam-non-nuoc	Bãi tắm Non Nước, bai tam non nuoc, bai-tam-non-nuoc, baitamnonnuoc	10	2023-09-14 00:29:44	2023-09-14 00:29:44
1c7c1835-6840-4c96-a5ee-951dc0acd432	f059dc67-723a-48db-af08-786a2dee0504	Thánh địa Mỹ Sơn	thanh-dia-my-son	Thánh địa Mỹ Sơn, thanh dia my son, thanh-dia-my-son, thanhdiamyson	10	2023-09-14 00:29:44	2023-09-14 00:29:44
40cde914-efac-4a8d-9203-666808355e86	f059dc67-723a-48db-af08-786a2dee0504	Cù Lao Chàm	cu-lao-cham	Cù Lao Chàm, cu lao cham, cu-lao-cham, culaocham	10	2023-09-14 00:29:44	2023-09-14 00:29:44
b51c209b-3409-42be-a299-ca34fd61f382	f059dc67-723a-48db-af08-786a2dee0504	Phố Cổ Hội An	pho-co-hoi-an	Phố Cổ Hội An, pho co hoi an, pho-co-hoi-an, phocohoian	10	2023-09-14 00:29:45	2023-09-14 00:29:45
998ed189-253c-4a0c-8f49-ab78926c3a25	f059dc67-723a-48db-af08-786a2dee0504	Làng gốm Thanh Hà	lang-gom-thanh-ha	Làng gốm Thanh Hà, lang gom thanh ha, lang-gom-thanh-ha, langgomthanhha	10	2023-09-14 00:29:45	2023-09-14 00:29:45
cb178321-d7bc-4fe1-8b60-d9381b8cfbf8	f059dc67-723a-48db-af08-786a2dee0504	Tượng đá Mẹ Thứ	tuong-da-me-thu	Tượng đá Mẹ Thứ, tuong da me thu, tuong-da-me-thu, tuongdamethu	10	2023-09-14 00:29:45	2023-09-14 00:29:45
a363717b-a44e-45b2-a560-c7fe35932e33	f059dc67-723a-48db-af08-786a2dee0504	Khe Lim	khe-lim	Khe Lim, khe lim, khe-lim, khelim	10	2023-09-14 00:29:45	2023-09-14 00:29:45
6182d9e1-bc25-4ac8-8c59-9d0e2ee548e4	f059dc67-723a-48db-af08-786a2dee0504	Bãi biển An Bàng	bai-bien-an-bang	Bãi biển An Bàng, bai bien an bang, bai-bien-an-bang, baibienanbang	10	2023-09-14 00:29:45	2023-09-14 00:29:45
577e3650-8868-4d55-b377-9f49f7bf9883	f059dc67-723a-48db-af08-786a2dee0504	Bãi biển Hà My	bai-bien-ha-my	Bãi biển Hà My, bai bien ha my, bai-bien-ha-my, baibienhamy	10	2023-09-14 00:29:46	2023-09-14 00:29:46
2611e00b-322b-4896-819b-054d1915d6b5	f059dc67-723a-48db-af08-786a2dee0504	Đồi chè Đông Giang	doi-che-dong-giang	Đồi chè Đông Giang, doi che dong giang, doi-che-dong-giang, doichedonggiang	10	2023-09-14 00:29:46	2023-09-14 00:29:46
6dbab123-0809-4f2a-adb0-e36a60a7181f	f059dc67-723a-48db-af08-786a2dee0504	Rừng dừa bảy mẫu	rung-dua-bay-mau	Rừng dừa bảy mẫu, rung dua bay mau, rung-dua-bay-mau, rungduabaymau	10	2023-09-14 00:29:46	2023-09-14 00:29:46
f6c5a0d6-bcd0-4f45-8ed9-25e61c52ca90	f059dc67-723a-48db-af08-786a2dee0504	Làng bích họa Tam Thanh	lang-bich-hoa-tam-thanh	Làng bích họa Tam Thanh, lang bich hoa tam thanh, lang-bich-hoa-tam-thanh, langbichhoatamthanh	10	2023-09-14 00:29:46	2023-09-14 00:29:46
0b7dc225-a7e2-4232-ba17-90ec9c038f65	f059dc67-723a-48db-af08-786a2dee0504	Bãi tắm Rạng	bai-tam-rang	Bãi tắm Rạng, bai tam rang, bai-tam-rang, baitamrang	10	2023-09-14 00:29:46	2023-09-14 00:29:46
e666405d-4799-425e-8fd5-9c71fe45278d	49f50bac-9925-4f1d-8579-2c7bcaf3d3a8	Đảo Lý Sơn	dao-ly-son	Đảo Lý Sơn, dao ly son, dao-ly-son, daolyson	10	2023-09-14 00:29:47	2023-09-14 00:29:47
e2ef45ff-e82c-448e-90be-d6da7681bc4f	49f50bac-9925-4f1d-8579-2c7bcaf3d3a8	Thác trắng Minh Long	thac-trang-minh-long	Thác trắng Minh Long, thac trang minh long, thac-trang-minh-long, thactrangminhlong	10	2023-09-14 00:29:47	2023-09-14 00:29:47
1349557a-5834-4f55-b3db-664776d78ef8	49f50bac-9925-4f1d-8579-2c7bcaf3d3a8	Đồng muối Sa Huỳnh	dong-muoi-sa-huynh	Đồng muối Sa Huỳnh, dong muoi sa huynh, dong-muoi-sa-huynh, dongmuoisahuynh	10	2023-09-14 00:29:47	2023-09-14 00:29:47
39cd44f2-2280-4ed8-adb3-1bdc456554f1	49f50bac-9925-4f1d-8579-2c7bcaf3d3a8	Mũi Ba làng An	mui-ba-lang-an	Mũi Ba làng An, mui ba lang an, mui-ba-lang-an, muibalangan	10	2023-09-14 00:29:47	2023-09-14 00:29:47
a9ce52cd-0ca6-493d-8c50-5358b42718df	49f50bac-9925-4f1d-8579-2c7bcaf3d3a8	Núi Cà Đam	nui-ca-dam	Núi Cà Đam, nui ca dam, nui-ca-dam, nuicadam	10	2023-09-14 00:29:47	2023-09-14 00:29:47
1eaaa41c-4ccb-449f-adc9-75e63fcfeb63	49f50bac-9925-4f1d-8579-2c7bcaf3d3a8	Đèo Vi Ô Lắc	deo-vi-o-lac	Đèo Vi Ô Lắc, deo vi o lac, deo-vi-o-lac, deoviolac	10	2023-09-14 00:29:47	2023-09-14 00:29:47
2570255f-af19-4fc8-8b91-115b74d94197	49f50bac-9925-4f1d-8579-2c7bcaf3d3a8	Khu bảo tồn thiên nhiên Kon Chư Răng	khu-bao-ton-thien-nhien-kon-chu-rang	Khu bảo tồn thiên nhiên Kon Chư Răng, khu bao ton thien nhien kon chu rang, khu-bao-ton-thien-nhien-kon-chu-rang, khubaotonthiennhienkonchurang	10	2023-09-14 00:29:48	2023-09-14 00:29:48
0fc7f42e-be16-420f-bf5a-36b148f76d55	49f50bac-9925-4f1d-8579-2c7bcaf3d3a8	Đèo Long Môn	deo-long-mon	Đèo Long Môn, deo long mon, deo-long-mon, deolongmon	10	2023-09-14 00:29:48	2023-09-14 00:29:48
8dc1188d-cdc2-48de-9194-5dc7081a1a48	a3febc40-c408-4d66-95c1-0983cc0a2e77	Cù Lao Xanh	cu-lao-xanh	Cù Lao Xanh, cu lao xanh, cu-lao-xanh, culaoxanh	10	2023-09-14 00:29:48	2023-09-14 00:29:48
6633e19a-a8dc-4f62-bdfa-6de94a19a1ac	a3febc40-c408-4d66-95c1-0983cc0a2e77	Quy Nhơn	quy-nhon	Quy Nhơn, quy nhon, quy-nhon, quynhon	10	2023-09-14 00:29:48	2023-09-14 00:29:48
8f74e5ff-b6e8-4830-93ad-a7d2a9ecf015	a3febc40-c408-4d66-95c1-0983cc0a2e77	Hòn Khô	hon-kho	Hòn Khô, hon kho, hon-kho, honkho	10	2023-09-14 00:29:48	2023-09-14 00:29:48
c29ce6f6-d71a-4acd-8aa7-a985c4b89abe	a3febc40-c408-4d66-95c1-0983cc0a2e77	Kỳ Co	ky-co	Kỳ Co, ky co, ky-co, kyco	10	2023-09-14 00:29:49	2023-09-14 00:29:49
e70b723e-ca9d-4501-a23f-a6904d572d06	a3febc40-c408-4d66-95c1-0983cc0a2e77	Eo Gió	eo-gio	Eo Gió, eo gio, eo-gio, eogio	10	2023-09-14 00:29:49	2023-09-14 00:29:49
c9dc9dcc-30e4-4253-8cf7-25d7a32a1a3d	a3febc40-c408-4d66-95c1-0983cc0a2e77	Khu du lịch Trung Lương	khu-du-lich-trung-luong	Khu du lịch Trung Lương, khu du lich trung luong, khu-du-lich-trung-luong, khudulichtrungluong	10	2023-09-14 00:29:49	2023-09-14 00:29:49
5bec108c-35ba-4ea3-be6b-e726c3716f19	a3febc40-c408-4d66-95c1-0983cc0a2e77	Đảo Yến	dao-yen	Đảo Yến, dao yen, dao-yen, daoyen	10	2023-09-14 00:29:49	2023-09-14 00:29:49
f7525cba-aa5b-4ffa-b56a-fe8654c0e89a	a3febc40-c408-4d66-95c1-0983cc0a2e77	Mũi Vi Rồng	mui-vi-rong	Mũi Vi Rồng, mui vi rong, mui-vi-rong, muivirong	10	2023-09-14 00:29:49	2023-09-14 00:29:49
203013dd-c937-427a-bead-0ff059adb81c	a3febc40-c408-4d66-95c1-0983cc0a2e77	Biển Quy Hòa	bien-quy-hoa	Biển Quy Hòa, bien quy hoa, bien-quy-hoa, bienquyhoa	10	2023-09-14 00:29:49	2023-09-14 00:29:49
b18ebd99-fd63-4faa-9697-b4c81160be9e	90379627-06fa-4e12-b835-9991b3bc96c7	Gành Đá Dĩa	ganh-da-dia	Gành Đá Dĩa, ganh da dia, ganh-da-dia, ganhdadia	10	2023-09-14 00:29:50	2023-09-14 00:29:50
53c46bcb-dd4f-4ece-a33d-b04cfb7201c9	90379627-06fa-4e12-b835-9991b3bc96c7	Bãi Xép	bai-xep	Bãi Xép, bai xep, bai-xep, baixep	10	2023-09-14 00:29:50	2023-09-14 00:29:50
37bf4402-9198-4331-859a-499361c2fa92	90379627-06fa-4e12-b835-9991b3bc96c7	Đập Tam Giang	dap-tam-giang	Đập Tam Giang, dap tam giang, dap-tam-giang, daptamgiang	10	2023-09-14 00:29:50	2023-09-14 00:29:50
e89e46f3-c135-4f42-8b96-e2cd8c8e311a	90379627-06fa-4e12-b835-9991b3bc96c7	Bãi Môn – Mũi Diện	bai-mon-mui-dien	Bãi Môn – Mũi Diện, bai mon – mui dien, bai-mon-mui-dien, baimonmuidien	10	2023-09-14 00:29:50	2023-09-14 00:29:50
9d6c5ed3-e100-49ec-a985-926d3771509d	90379627-06fa-4e12-b835-9991b3bc96c7	Đầm Ô Loan	dam-o-loan	Đầm Ô Loan, dam o loan, dam-o-loan, damoloan	10	2023-09-14 00:29:51	2023-09-14 00:29:51
83b89864-5b35-4942-8bfc-d1e993cb46f3	90379627-06fa-4e12-b835-9991b3bc96c7	Vịnh Xuân Đài	vinh-xuan-dai	Vịnh Xuân Đài, vinh xuan dai, vinh-xuan-dai, vinhxuandai	10	2023-09-14 00:29:51	2023-09-14 00:29:51
445749bf-ab73-490b-ae63-ee7f824d4d67	90379627-06fa-4e12-b835-9991b3bc96c7	Tháp Nhạn	thap-nhan	Tháp Nhạn, thap nhan, thap-nhan, thapnhan	10	2023-09-14 00:29:51	2023-09-14 00:29:51
6bbe1e62-dcc6-44da-983d-6c17f374710a	90379627-06fa-4e12-b835-9991b3bc96c7	Hòn Nưa	hon-nua	Hòn Nưa, hon nua, hon-nua, honnua	10	2023-09-14 00:29:51	2023-09-14 00:29:51
27e8a814-35eb-4b55-afe7-49e1bdf31101	1410027e-2963-4933-875f-b4ef12e39375	Khu du lịch Vinpearl Land	khu-du-lich-vinpearl-land	Khu du lịch Vinpearl Land, khu du lich vinpearl land, khu-du-lich-vinpearl-land, khudulichvinpearlland	10	2023-09-14 00:29:51	2023-09-14 00:29:51
93df5e0d-7b33-4d18-bb77-e6713fa05215	1410027e-2963-4933-875f-b4ef12e39375	Viện Hải dương học Nha Trang	vien-hai-duong-hoc-nha-trang	Viện Hải dương học Nha Trang, vien hai duong hoc nha trang, vien-hai-duong-hoc-nha-trang, vienhaiduonghocnhatrang	10	2023-09-14 00:29:52	2023-09-14 00:29:52
0c329925-4bda-4ff6-b148-9e4a7123e5c2	1410027e-2963-4933-875f-b4ef12e39375	Đảo Hòn Mun	dao-hon-mun	Đảo Hòn Mun, dao hon mun, dao-hon-mun, daohonmun	10	2023-09-14 00:29:52	2023-09-14 00:29:52
48829197-0975-4fa6-a66c-1c62e5a24285	1410027e-2963-4933-875f-b4ef12e39375	Bãi biển Đại Lãnh	bai-bien-dai-lanh	Bãi biển Đại Lãnh, bai bien dai lanh, bai-bien-dai-lanh, baibiendailanh	10	2023-09-14 00:29:52	2023-09-14 00:29:52
502998b0-4ee4-4acb-bda5-da2f2848b501	1410027e-2963-4933-875f-b4ef12e39375	Khu du lịch Dốc Lết	khu-du-lich-doc-let	Khu du lịch Dốc Lết, khu du lich doc let, khu-du-lich-doc-let, khudulichdoclet	10	2023-09-14 00:29:52	2023-09-14 00:29:52
40c3bc1d-4fdd-4d53-85d9-986fefbee6b2	1410027e-2963-4933-875f-b4ef12e39375	Tháp Bà Po Nagar	thap-ba-po-nagar	Tháp Bà Po Nagar, thap ba po nagar, thap-ba-po-nagar, thapbaponagar	10	2023-09-14 00:29:52	2023-09-14 00:29:52
39300225-a449-4d77-97b8-973622ef8f86	1410027e-2963-4933-875f-b4ef12e39375	Thác Yangbay	thac-yangbay	Thác Yangbay, thac yangbay, thac-yangbay, thacyangbay	10	2023-09-14 00:29:53	2023-09-14 00:29:53
9b6765c3-59c3-475c-854c-dff114912ba6	1410027e-2963-4933-875f-b4ef12e39375	Bãi biển Nha Trang	bai-bien-nha-trang	Bãi biển Nha Trang, bai bien nha trang, bai-bien-nha-trang, baibiennhatrang	10	2023-09-14 00:29:53	2023-09-14 00:29:53
03474fc9-cc99-4f79-b341-c31d6f43d75a	1410027e-2963-4933-875f-b4ef12e39375	Đảo Bình Hưng	dao-binh-hung	Đảo Bình Hưng, dao binh hung, dao-binh-hung, daobinhhung	10	2023-09-14 00:29:53	2023-09-14 00:29:53
0d05601c-e7ce-4d72-8668-011e70d01ca7	1410027e-2963-4933-875f-b4ef12e39375	Đảo Bình Ba	dao-binh-ba	Đảo Bình Ba, dao binh ba, dao-binh-ba, daobinhba	10	2023-09-14 00:29:53	2023-09-14 00:29:53
2df85ed0-048f-4d75-ad2f-30f47a0ac8e0	1410027e-2963-4933-875f-b4ef12e39375	Đảo Điệp Sơn	dao-diep-son	Đảo Điệp Sơn, dao diep son, dao-diep-son, daodiepson	10	2023-09-14 00:29:53	2023-09-14 00:29:53
80b496c7-e445-400a-8401-7bfd0e2e0fd0	1410027e-2963-4933-875f-b4ef12e39375	Hòn Tre	hon-tre	Hòn Tre, hon tre, hon-tre, hontre	10	2023-09-14 00:29:53	2023-09-14 00:29:53
70394cad-8e98-457b-82f0-7faa1f491d77	1410027e-2963-4933-875f-b4ef12e39375	Hồ Đá Bàn	ho-da-ban	Hồ Đá Bàn, ho da ban, ho-da-ban, hodaban	10	2023-09-14 00:29:54	2023-09-14 00:29:54
05552a72-555f-45de-8bcb-d7b8806d017f	1410027e-2963-4933-875f-b4ef12e39375	Đỉnh Hòn Bà	dinh-hon-ba	Đỉnh Hòn Bà, dinh hon ba, dinh-hon-ba, dinhhonba	10	2023-09-14 00:29:54	2023-09-14 00:29:54
8f438351-4875-49c5-811d-abfba301ffc0	1410027e-2963-4933-875f-b4ef12e39375	Hòn Ông	hon-ong	Hòn Ông, hon ong, hon-ong, honong	10	2023-09-14 00:29:54	2023-09-14 00:29:54
d567e26b-3f8d-475a-90a1-876867723ec9	1410027e-2963-4933-875f-b4ef12e39375	Mũi Đôi	mui-doi	Mũi Đôi, mui doi, mui-doi, muidoi	10	2023-09-14 00:29:54	2023-09-14 00:29:54
603b6abb-3418-43c1-9dc1-ba069b493528	1410027e-2963-4933-875f-b4ef12e39375	Đầm Môn	dam-mon	Đầm Môn, dam mon, dam-mon, dammon	10	2023-09-14 00:29:54	2023-09-14 00:29:54
0ae876f4-11c4-41ef-8ecb-7391e5d80403	1410027e-2963-4933-875f-b4ef12e39375	Suối Ba Hồ	suoi-ba-ho	Suối Ba Hồ, suoi ba ho, suoi-ba-ho, suoibaho	10	2023-09-14 00:29:55	2023-09-14 00:29:55
889e2b60-ef39-4482-a784-46ec668acd71	1410027e-2963-4933-875f-b4ef12e39375	Khu du lịch Con Sẻ Tre	khu-du-lich-con-se-tre	Khu du lịch Con Sẻ Tre, khu du lich con se tre, khu-du-lich-con-se-tre, khudulichconsetre	10	2023-09-14 00:29:55	2023-09-14 00:29:55
f86d821e-7f1d-41e8-909c-1200bee7e714	1410027e-2963-4933-875f-b4ef12e39375	Suối Đổ	suoi-do	Suối Đổ, suoi do, suoi-do, suoido	10	2023-09-14 00:29:55	2023-09-14 00:29:55
8ce9b9e5-d56d-48e1-8567-b1a43f4a82f0	1410027e-2963-4933-875f-b4ef12e39375	Khu du lịch suối Thạch Lâm	khu-du-lich-suoi-thach-lam	Khu du lịch suối Thạch Lâm, khu du lich suoi thach lam, khu-du-lich-suoi-thach-lam, khudulichsuoithachlam	10	2023-09-14 00:29:55	2023-09-14 00:29:55
00f24524-b070-4b40-ab8e-c22e46e150e0	c2e1fa4d-be33-4b16-916b-c59118362930	Vịnh Vĩnh Hy	vinh-vinh-hy	Vịnh Vĩnh Hy, vinh vinh hy, vinh-vinh-hy, vinhvinhhy	10	2023-09-14 00:29:55	2023-09-14 00:29:55
2d325844-b697-4381-a4e4-83f4584b4ed0	c2e1fa4d-be33-4b16-916b-c59118362930	Hang Rái	hang-rai	Hang Rái, hang rai, hang-rai, hangrai	10	2023-09-14 00:29:56	2023-09-14 00:29:56
de8792c9-f5c0-4218-be41-804a24a34ff8	c2e1fa4d-be33-4b16-916b-c59118362930	Làng Mông Cổ	lang-mong-co	Làng Mông Cổ, lang mong co, lang-mong-co, langmongco	10	2023-09-14 00:29:56	2023-09-14 00:29:56
65b5a88a-0ceb-45c5-a7e4-abd653bc0bbd	c2e1fa4d-be33-4b16-916b-c59118362930	Thác Chap Pơ	thac-chap-po	Thác Chap Pơ, thac chap po, thac-chap-po, thacchappo	10	2023-09-14 00:29:56	2023-09-14 00:29:56
ccc0cd77-33e4-40b4-b487-19a62cf5a4df	c2e1fa4d-be33-4b16-916b-c59118362930	Hòn Đỏ	hon-do	Hòn Đỏ, hon do, hon-do, hondo	10	2023-09-14 00:29:56	2023-09-14 00:29:56
a5a4f836-e902-4763-9f50-e033dc0a717b	c2e1fa4d-be33-4b16-916b-c59118362930	Hải Đăng Mũi Dinh	hai-dang-mui-dinh	Hải Đăng Mũi Dinh, hai dang mui dinh, hai-dang-mui-dinh, haidangmuidinh	10	2023-09-14 00:29:56	2023-09-14 00:29:56
5ef6f01f-04d8-4c0f-97fb-81916a6e237a	08ae3265-5573-4f25-9bb9-9c996898f439	Biển Cổ Thạch	bien-co-thach	Biển Cổ Thạch, bien co thach, bien-co-thach, biencothach	10	2023-09-14 00:29:57	2023-09-14 00:29:57
8a9b9c0e-8da5-4254-8933-f6d408fbd755	08ae3265-5573-4f25-9bb9-9c996898f439	Khu du lịch Mũi Né	khu-du-lich-mui-ne	Khu du lịch Mũi Né, khu du lich mui ne, khu-du-lich-mui-ne, khudulichmuine	10	2023-09-14 00:29:57	2023-09-14 00:29:57
038c8c58-daa6-417d-b830-1851fd4a5a4d	08ae3265-5573-4f25-9bb9-9c996898f439	Tháp Chàm	thap-cham	Tháp Chàm, thap cham, thap-cham, thapcham	10	2023-09-14 00:29:57	2023-09-14 00:29:57
d3b1dc19-d6ad-450f-b98b-cabec558dfd1	08ae3265-5573-4f25-9bb9-9c996898f439	Coco Beach Camp	coco-beach-camp	Coco Beach Camp, coco beach camp, coco-beach-camp, cocobeachcamp	10	2023-09-14 00:29:57	2023-09-14 00:29:57
cecd7ccd-59d7-4809-bb83-03584ddd8025	08ae3265-5573-4f25-9bb9-9c996898f439	Cù Lao Câu – Tuy Phong	cu-lao-cau-tuy-phong	Cù Lao Câu – Tuy Phong, cu lao cau – tuy phong, cu-lao-cau-tuy-phong, culaocautuyphong	10	2023-09-14 00:29:57	2023-09-14 00:29:57
0203c1cd-42a2-4bbf-a0a3-e339ab498fad	2a7ec72d-f7f4-44ea-b16b-18d63e3fa9ec	Núi Bà Đen	nui-ba-den	Núi Bà Đen, nui ba den, nui-ba-den, nuibaden	10	2023-09-14 00:30:11	2023-09-14 00:30:11
9726abec-9a16-41d1-8841-6de8b1b43079	08ae3265-5573-4f25-9bb9-9c996898f439	Đảo Phú Quý – Phan Thiết	dao-phu-quy-phan-thiet	Đảo Phú Quý – Phan Thiết, dao phu quy – phan thiet, dao-phu-quy-phan-thiet, daophuquyphanthiet	10	2023-09-14 00:29:58	2023-09-14 00:29:58
b37fc1ae-572e-4fad-998f-8d4101727809	08ae3265-5573-4f25-9bb9-9c996898f439	Mũi Kê Gà – Thuận Quý	mui-ke-ga-thuan-quy	Mũi Kê Gà – Thuận Quý, mui ke ga – thuan quy, mui-ke-ga-thuan-quy, muikegathuanquy	10	2023-09-14 00:29:58	2023-09-14 00:29:58
1d993322-0b82-452f-9234-1c85164746d0	08ae3265-5573-4f25-9bb9-9c996898f439	Công Viên Thượng Cát	cong-vien-thuong-cat	Công Viên Thượng Cát, cong vien thuong cat, cong-vien-thuong-cat, congvienthuongcat	10	2023-09-14 00:29:58	2023-09-14 00:29:58
4971945d-9469-4fb5-9f40-c2591b93b96f	4ae40be9-eb65-4fa2-b8c6-a447c51f2231	Hồ T’Nưng	ho-tnung	Hồ T’Nưng, ho t’nung, ho-tnung, hotnung	10	2023-09-14 00:29:58	2023-09-14 00:29:58
b31600c8-b481-4727-a591-121a9b2070ea	4ae40be9-eb65-4fa2-b8c6-a447c51f2231	Thác Chín Tầng	thac-chin-tang	Thác Chín Tầng, thac chin tang, thac-chin-tang, thacchintang	10	2023-09-14 00:29:58	2023-09-14 00:29:58
2ce7b156-cb48-47df-a5ec-943701f660c6	4ae40be9-eb65-4fa2-b8c6-a447c51f2231	Thác Phú Cường	thac-phu-cuong	Thác Phú Cường, thac phu cuong, thac-phu-cuong, thacphucuong	10	2023-09-14 00:29:59	2023-09-14 00:29:59
e4c2c2b0-c796-48da-baa6-f24e3c781aaa	4ae40be9-eb65-4fa2-b8c6-a447c51f2231	Chùa Minh Thành	chua-minh-thanh	Chùa Minh Thành, chua minh thanh, chua-minh-thanh, chuaminhthanh	10	2023-09-14 00:29:59	2023-09-14 00:29:59
bc656854-11d1-487e-b75a-4b2658756550	87276da2-a3ea-42b9-ad66-2322b356edd1	Rừng Thông Măng Đen	rung-thong-mang-den	Rừng Thông Măng Đen, rung thong mang den, rung-thong-mang-den, rungthongmangden	10	2023-09-14 00:29:59	2023-09-14 00:29:59
08409028-0364-4455-afbc-b3f8de80199b	87276da2-a3ea-42b9-ad66-2322b356edd1	Nhà Thờ Gỗ	nha-tho-go	Nhà Thờ Gỗ, nha tho go, nha-tho-go, nhathogo	10	2023-09-14 00:29:59	2023-09-14 00:29:59
844e4e58-3919-486d-8cf8-343ef6df7801	87276da2-a3ea-42b9-ad66-2322b356edd1	Vườn Quốc Gia Chư Mom Ray	vuon-quoc-gia-chu-mom-ray	Vườn Quốc Gia Chư Mom Ray, vuon quoc gia chu mom ray, vuon-quoc-gia-chu-mom-ray, vuonquocgiachumomray	10	2023-09-14 00:29:59	2023-09-14 00:29:59
307e50eb-b338-44c1-aa02-be9309493b0b	87276da2-a3ea-42b9-ad66-2322b356edd1	Núi Ngọc Linh	nui-ngoc-linh	Núi Ngọc Linh, nui ngoc linh, nui-ngoc-linh, nuingoclinh	10	2023-09-14 00:30:00	2023-09-14 00:30:00
3dc4cbac-76bb-4197-b510-7d4376666ad2	87276da2-a3ea-42b9-ad66-2322b356edd1	Ngã Ba Đồng Dương	nga-ba-dong-duong	Ngã Ba Đồng Dương, nga ba dong duong, nga-ba-dong-duong, ngabadongduong	10	2023-09-14 00:30:00	2023-09-14 00:30:00
863a5313-a63c-4116-a97e-33babd284457	87276da2-a3ea-42b9-ad66-2322b356edd1	Thác Pa Sỹ	thac-pa-sy	Thác Pa Sỹ, thac pa sy, thac-pa-sy, thacpasy	10	2023-09-14 00:30:00	2023-09-14 00:30:00
3372ebdc-0f2a-408a-a95a-c701dc76130f	bf13331c-6373-4ee7-8692-a98b27806ee8	Buôn Đôn	buon-don	Buôn Đôn, buon don, buon-don, buondon	10	2023-09-14 00:30:00	2023-09-14 00:30:00
5e8f9540-e1a4-414e-a9cc-e66335591ac3	bf13331c-6373-4ee7-8692-a98b27806ee8	Khu du lịch đồ Tâm Linh	khu-du-lich-do-tam-linh	Khu du lịch đồ Tâm Linh, khu du lich do tam linh, khu-du-lich-do-tam-linh, khudulichdotamlinh	10	2023-09-14 00:30:00	2023-09-14 00:30:00
accaf69d-f341-44cd-9c32-b8b4852fca1b	bf13331c-6373-4ee7-8692-a98b27806ee8	Vườn Quốc Gia Chư Yang Sin	vuon-quoc-gia-chu-yang-sin	Vườn Quốc Gia Chư Yang Sin, vuon quoc gia chu yang sin, vuon-quoc-gia-chu-yang-sin, vuonquocgiachuyangsin	10	2023-09-14 00:30:01	2023-09-14 00:30:01
cc1120b4-129e-4b13-9b1f-72ac3e3b3d17	bf13331c-6373-4ee7-8692-a98b27806ee8	Thác Chồng – Thác Vợ	thac-chong-thac-vo	Thác Chồng – Thác Vợ, thac chong – thac vo, thac-chong-thac-vo, thacchongthacvo	10	2023-09-14 00:30:01	2023-09-14 00:30:01
928cdd90-f2e8-4c60-ac79-2b8233cbb4b0	bf13331c-6373-4ee7-8692-a98b27806ee8	Thác Gia Long	thac-gia-long	Thác Gia Long, thac gia long, thac-gia-long, thacgialong	10	2023-09-14 00:30:01	2023-09-14 00:30:01
3344ff28-565c-4a6c-8b70-3cc23c9c9013	bf13331c-6373-4ee7-8692-a98b27806ee8	Thác Thủy Tiên	thac-thuy-tien	Thác Thủy Tiên, thac thuy tien, thac-thuy-tien, thacthuytien	10	2023-09-14 00:30:01	2023-09-14 00:30:01
76ea5291-80bc-4632-a4d2-9f0855ef5a37	bf13331c-6373-4ee7-8692-a98b27806ee8	Thác Bảy Nhánh	thac-bay-nhanh	Thác Bảy Nhánh, thac bay nhanh, thac-bay-nhanh, thacbaynhanh	10	2023-09-14 00:30:01	2023-09-14 00:30:01
8db1abe5-f7e8-4b7b-918a-1fcf7d615ea9	bf13331c-6373-4ee7-8692-a98b27806ee8	Thác Krông KMar	thac-krong-kmar	Thác Krông KMar, thac krong kmar, thac-krong-kmar, thackrongkmar	10	2023-09-14 00:30:02	2023-09-14 00:30:02
3624f047-7dca-4eb5-aae9-b8468c9b575a	21ea4e6a-bebf-4706-96a4-257144891137	Thác Pongour	thac-pongour	Thác Pongour, thac pongour, thac-pongour, thacpongour	10	2023-09-14 00:30:02	2023-09-14 00:30:02
791a7b0c-1371-487d-8a45-55982bd7b7d7	21ea4e6a-bebf-4706-96a4-257144891137	Khu sinh thái Đa Mê	khu-sinh-thai-da-me	Khu sinh thái Đa Mê, khu sinh thai da me, khu-sinh-thai-da-me, khusinhthaidame	10	2023-09-14 00:30:02	2023-09-14 00:30:02
0d06243e-298d-427a-b4b3-1e56e0978afd	21ea4e6a-bebf-4706-96a4-257144891137	Khu du lịch Trần Lê Gia Trang	khu-du-lich-tran-le-gia-trang	Khu du lịch Trần Lê Gia Trang, khu du lich tran le gia trang, khu-du-lich-tran-le-gia-trang, khudulichtranlegiatrang	10	2023-09-14 00:30:02	2023-09-14 00:30:02
0f27a940-53c1-4aa9-af4f-dfd4418c5260	21ea4e6a-bebf-4706-96a4-257144891137	Khu du lịch Đà Lạt	khu-du-lich-da-lat	Khu du lịch Đà Lạt, khu du lich da lat, khu-du-lich-da-lat, khudulichdalat	10	2023-09-14 00:30:02	2023-09-14 00:30:02
d7ed71f4-3db4-4349-a850-8d33fbc469cb	21ea4e6a-bebf-4706-96a4-257144891137	Đồi cỏ và rừng Tà Năng	doi-co-va-rung-ta-nang	Đồi cỏ và rừng Tà Năng, doi co va rung ta nang, doi-co-va-rung-ta-nang, doicovarungtanang	10	2023-09-14 00:30:03	2023-09-14 00:30:03
443b4f00-6d8f-4b95-8353-03024c66f5c4	21ea4e6a-bebf-4706-96a4-257144891137	Hồ Đại Ninh	ho-dai-ninh	Hồ Đại Ninh, ho dai ninh, ho-dai-ninh, hodaininh	10	2023-09-14 00:30:03	2023-09-14 00:30:03
15e7311e-3d06-4c11-b42b-3da523ae9631	21ea4e6a-bebf-4706-96a4-257144891137	Thác Jraiblian	thac-jraiblian	Thác Jraiblian, thac jraiblian, thac-jraiblian, thacjraiblian	10	2023-09-14 00:30:03	2023-09-14 00:30:03
0d0de384-d1af-4c00-8b45-0fde2b0aae26	21ea4e6a-bebf-4706-96a4-257144891137	Làng K’Long	lang-klong	Làng K’Long, lang k’long, lang-klong, langklong	10	2023-09-14 00:30:03	2023-09-14 00:30:03
0c094377-bdef-457f-b622-2cd37b14342a	b378bf39-dcad-428b-a6b3-34665208a4f5	Khu du lịch sinh thái Nâm Nung	khu-du-lich-sinh-thai-nam-nung	Khu du lịch sinh thái Nâm Nung, khu du lich sinh thai nam nung, khu-du-lich-sinh-thai-nam-nung, khudulichsinhthainamnung	10	2023-09-14 00:30:03	2023-09-14 00:30:03
cc522dc0-cb22-49bc-a406-b45a4f02fa84	b378bf39-dcad-428b-a6b3-34665208a4f5	Hồ Ea Sno	ho-ea-sno	Hồ Ea Sno, ho ea sno, ho-ea-sno, hoeasno	10	2023-09-14 00:30:04	2023-09-14 00:30:04
7750512d-6444-416a-a6b1-82865e20c511	b378bf39-dcad-428b-a6b3-34665208a4f5	Hang động núi lửa Chư Bluk	hang-dong-nui-lua-chu-bluk	Hang động núi lửa Chư Bluk, hang dong nui lua chu bluk, hang-dong-nui-lua-chu-bluk, hangdongnuiluachubluk	10	2023-09-14 00:30:04	2023-09-14 00:30:04
a5a76f6e-a1a0-45c0-82b0-1c2e21a3d88a	2a7ec72d-f7f4-44ea-b16b-18d63e3fa9ec	Tháp cổ Bình Thạnh	thap-co-binh-thanh	Tháp cổ Bình Thạnh, thap co binh thanh, thap-co-binh-thanh, thapcobinhthanh	10	2023-09-14 00:30:11	2023-09-14 00:30:11
caa63005-6c9b-49ea-a24a-1db144b34f7f	b378bf39-dcad-428b-a6b3-34665208a4f5	Khu du lịch cụm thác Đray – Gia Long – Trinh Nữ	khu-du-lich-cum-thac-dray-gia-long-trinh-nu	Khu du lịch cụm thác Đray – Gia Long – Trinh Nữ, khu du lich cum thac dray – gia long – trinh nu, khu-du-lich-cum-thac-dray-gia-long-trinh-nu, khudulichcumthacdraygialongtrinhnu	10	2023-09-14 00:30:04	2023-09-14 00:30:04
b6e6cccc-8fba-4b9d-aaf5-1c26a746a2f5	b378bf39-dcad-428b-a6b3-34665208a4f5	Chùa Pháp Hoa	chua-phap-hoa	Chùa Pháp Hoa, chua phap hoa, chua-phap-hoa, chuaphaphoa	10	2023-09-14 00:30:04	2023-09-14 00:30:04
f93f7123-ee6e-41d7-a03e-656c8953459e	b378bf39-dcad-428b-a6b3-34665208a4f5	Thác Diệu Thanh	thac-dieu-thanh	Thác Diệu Thanh, thac dieu thanh, thac-dieu-thanh, thacdieuthanh	10	2023-09-14 00:30:04	2023-09-14 00:30:04
b5acda51-f28f-49a8-8085-53b963e1a1db	b378bf39-dcad-428b-a6b3-34665208a4f5	Thác Đắk G’Lun	thac-dak-glun	Thác Đắk G’Lun, thac dak g’lun, thac-dak-glun, thacdakglun	10	2023-09-14 00:30:05	2023-09-14 00:30:05
b6f0342b-f02b-4d71-a069-ed0b78c1eb03	b378bf39-dcad-428b-a6b3-34665208a4f5	Thác Đắk Buk So	thac-dak-buk-so	Thác Đắk Buk So, thac dak buk so, thac-dak-buk-so, thacdakbukso	10	2023-09-14 00:30:05	2023-09-14 00:30:05
0cb7b4f1-cc9c-416f-8879-f49989f4e836	a471fd3a-ad84-4bdc-a993-629f5d28c808	Chợ Bến Thành	cho-ben-thanh	Chợ Bến Thành, cho ben thanh, cho-ben-thanh, chobenthanh	10	2023-09-14 00:30:05	2023-09-14 00:30:05
21447732-6587-48b6-b024-466299c8ca2f	a471fd3a-ad84-4bdc-a993-629f5d28c808	Nhà Thờ Đức Bà	nha-tho-duc-ba	Nhà Thờ Đức Bà, nha tho duc ba, nha-tho-duc-ba, nhathoducba	10	2023-09-14 00:30:05	2023-09-14 00:30:05
c6fa2032-efb2-4c63-bf2a-550476e0a135	a471fd3a-ad84-4bdc-a993-629f5d28c808	Địa Đạo Củ Chi	dia-dao-cu-chi	Địa Đạo Củ Chi, dia dao cu chi, dia-dao-cu-chi, diadaocuchi	10	2023-09-14 00:30:05	2023-09-14 00:30:05
42e0d278-7f80-455b-a90a-0ef3a87ee085	a471fd3a-ad84-4bdc-a993-629f5d28c808	Bưu Điện trung tâm Sài Gòn	buu-dien-trung-tam-sai-gon	Bưu Điện trung tâm Sài Gòn, buu dien trung tam sai gon, buu-dien-trung-tam-sai-gon, buudientrungtamsaigon	10	2023-09-14 00:30:06	2023-09-14 00:30:06
0837776e-28a0-4c94-b486-f84d56bd3907	a471fd3a-ad84-4bdc-a993-629f5d28c808	Nhà Hát Lớn	nha-hat-lon	Nhà Hát Lớn, nha hat lon, nha-hat-lon, nhahatlon	10	2023-09-14 00:30:06	2023-09-14 00:30:06
d4fe8ab6-516c-4291-8713-cfdd3e8b760f	a471fd3a-ad84-4bdc-a993-629f5d28c808	Sài Gòn Square	sai-gon-square	Sài Gòn Square, sai gon square, sai-gon-square, saigonsquare	10	2023-09-14 00:30:06	2023-09-14 00:30:06
db0b9467-98fa-4e6b-8121-02485e1b078b	a471fd3a-ad84-4bdc-a993-629f5d28c808	Cầu Ánh Sao	cau-anh-sao	Cầu Ánh Sao, cau anh sao, cau-anh-sao, cauanhsao	10	2023-09-14 00:30:06	2023-09-14 00:30:06
08e0d260-101e-41d7-89f3-52d94fcf5c8d	a471fd3a-ad84-4bdc-a993-629f5d28c808	Dinh Độc Lập	dinh-doc-lap	Dinh Độc Lập, dinh doc lap, dinh-doc-lap, dinhdoclap	10	2023-09-14 00:30:06	2023-09-14 00:30:06
19e82a1a-50dc-4346-ad90-c28e7c38e5ce	a471fd3a-ad84-4bdc-a993-629f5d28c808	Khu du lịch văn hóa Suối Tiên	khu-du-lich-van-hoa-suoi-tien	Khu du lịch văn hóa Suối Tiên, khu du lich van hoa suoi tien, khu-du-lich-van-hoa-suoi-tien, khudulichvanhoasuoitien	10	2023-09-14 00:30:07	2023-09-14 00:30:07
7e39f214-4d4a-4d56-9f83-ac7e2c24c192	a471fd3a-ad84-4bdc-a993-629f5d28c808	Phố Tây	pho-tay	Phố Tây, pho tay, pho-tay, photay	10	2023-09-14 00:30:07	2023-09-14 00:30:07
6a6aa890-068c-416e-8466-617e319157e4	a471fd3a-ad84-4bdc-a993-629f5d28c808	Chợ Lớn	cho-lon	Chợ Lớn, cho lon, cho-lon, cholon	10	2023-09-14 00:30:07	2023-09-14 00:30:07
8975b980-9541-4744-b482-34ccebf4b134	a471fd3a-ad84-4bdc-a993-629f5d28c808	Hầm Thủ Thiên	ham-thu-thien	Hầm Thủ Thiên, ham thu thien, ham-thu-thien, hamthuthien	10	2023-09-14 00:30:07	2023-09-14 00:30:07
3cdb93b0-9fe3-40b6-bca5-cd027bca1920	c2b15328-52af-4787-ac59-641d15d3a7d3	Khu du lịch Đại Nam	khu-du-lich-dai-nam	Khu du lịch Đại Nam, khu du lich dai nam, khu-du-lich-dai-nam, khudulichdainam	10	2023-09-14 00:30:08	2023-09-14 00:30:08
7357a1d8-178c-4a9f-85b5-b9c98c67c287	c2b15328-52af-4787-ac59-641d15d3a7d3	Chùa Hội Khánh	chua-hoi-khanh	Chùa Hội Khánh, chua hoi khanh, chua-hoi-khanh, chuahoikhanh	10	2023-09-14 00:30:08	2023-09-14 00:30:08
8fe5c4ad-28ae-4c84-ad82-d9ed574c6365	c2b15328-52af-4787-ac59-641d15d3a7d3	Cà Phê Gió và Nước	ca-phe-gio-va-nuoc	Cà Phê Gió và Nước, ca phe gio va nuoc, ca-phe-gio-va-nuoc, caphegiovanuoc	10	2023-09-14 00:30:08	2023-09-14 00:30:08
1a57ed7b-7f42-4b15-9e46-e01d336a45d5	c2b15328-52af-4787-ac59-641d15d3a7d3	Khu du lịch Thủy Châu	khu-du-lich-thuy-chau	Khu du lịch Thủy Châu, khu du lich thuy chau, khu-du-lich-thuy-chau, khudulichthuychau	10	2023-09-14 00:30:08	2023-09-14 00:30:08
decbf244-ce5f-4f7c-bb92-65e889d60249	c2b15328-52af-4787-ac59-641d15d3a7d3	Chùa Châu Thới	chua-chau-thoi	Chùa Châu Thới, chua chau thoi, chua-chau-thoi, chuachauthoi	10	2023-09-14 00:30:08	2023-09-14 00:30:08
eea7f8b6-269b-4b1a-b4e7-18c03404d470	c2b15328-52af-4787-ac59-641d15d3a7d3	Làng Tre Phú An	lang-tre-phu-an	Làng Tre Phú An, lang tre phu an, lang-tre-phu-an, langtrephuan	10	2023-09-14 00:30:08	2023-09-14 00:30:08
52e0b63e-39b7-45f5-877d-4f623ddfc632	c2b15328-52af-4787-ac59-641d15d3a7d3	Công viên thành phố mới Bình Dương	cong-vien-thanh-pho-moi-binh-duong	Công viên thành phố mới Bình Dương, cong vien thanh pho moi binh duong, cong-vien-thanh-pho-moi-binh-duong, congvienthanhphomoibinhduong	10	2023-09-14 00:30:09	2023-09-14 00:30:09
03a0cd94-5567-4c59-a7c9-5a41a321be13	c2b15328-52af-4787-ac59-641d15d3a7d3	Nhà thờ Chánh Tòa Phú Cường	nha-tho-chanh-toa-phu-cuong	Nhà thờ Chánh Tòa Phú Cường, nha tho chanh toa phu cuong, nha-tho-chanh-toa-phu-cuong, nhathochanhtoaphucuong	10	2023-09-14 00:30:09	2023-09-14 00:30:09
156c8950-f205-4b81-95b1-2a78a974e6a3	582dbcb3-5113-4350-9c5a-fa98d9b159b9	Rừng cao su Bình Phước	rung-cao-su-binh-phuoc	Rừng cao su Bình Phước, rung cao su binh phuoc, rung-cao-su-binh-phuoc, rungcaosubinhphuoc	10	2023-09-14 00:30:09	2023-09-14 00:30:09
9cf0790f-a39a-4f5c-8a8d-1aed5e1dae09	582dbcb3-5113-4350-9c5a-fa98d9b159b9	Hồ Suối Lam	ho-suoi-lam	Hồ Suối Lam, ho suoi lam, ho-suoi-lam, hosuoilam	10	2023-09-14 00:30:09	2023-09-14 00:30:09
dd31e663-ce97-415c-9a90-dbf7f019be45	582dbcb3-5113-4350-9c5a-fa98d9b159b9	Núi Bà Rá	nui-ba-ra	Núi Bà Rá, nui ba ra, nui-ba-ra, nuibara	10	2023-09-14 00:30:09	2023-09-14 00:30:09
3d6ab879-b3a0-44f9-a6ba-fe592407d8cb	582dbcb3-5113-4350-9c5a-fa98d9b159b9	Trảng Cỏ Bù Lạch	trang-co-bu-lach	Trảng Cỏ Bù Lạch, trang co bu lach, trang-co-bu-lach, trangcobulach	10	2023-09-14 00:30:10	2023-09-14 00:30:10
6a678953-9ce4-43cb-8420-7a5cb2e0de10	582dbcb3-5113-4350-9c5a-fa98d9b159b9	Vườn Quốc Gia Bù Gia Mập	vuon-quoc-gia-bu-gia-map	Vườn Quốc Gia Bù Gia Mập, vuon quoc gia bu gia map, vuon-quoc-gia-bu-gia-map, vuonquocgiabugiamap	10	2023-09-14 00:30:10	2023-09-14 00:30:10
c9c7d4ae-72c7-43b6-a696-808c80d91a88	582dbcb3-5113-4350-9c5a-fa98d9b159b9	Hồ suối giai	ho-suoi-giai	Hồ suối giai, ho suoi giai, ho-suoi-giai, hosuoigiai	10	2023-09-14 00:30:10	2023-09-14 00:30:10
b311933d-df4b-4285-ba8c-5f0a5eb464b0	582dbcb3-5113-4350-9c5a-fa98d9b159b9	Hồ Thác Mơ	ho-thac-mo	Hồ Thác Mơ, ho thac mo, ho-thac-mo, hothacmo	10	2023-09-14 00:30:10	2023-09-14 00:30:10
f8d27af8-89ec-4031-9301-7fb11864e5fb	582dbcb3-5113-4350-9c5a-fa98d9b159b9	Thác Đứng	thac-dung	Thác Đứng, thac dung, thac-dung, thacdung	10	2023-09-14 00:30:10	2023-09-14 00:30:10
f1d10b1b-d2c6-49d5-9f99-869cddf92c8b	2a7ec72d-f7f4-44ea-b16b-18d63e3fa9ec	Cửa khẩu Mộc Bài	cua-khau-moc-bai	Cửa khẩu Mộc Bài, cua khau moc bai, cua-khau-moc-bai, cuakhaumocbai	10	2023-09-14 00:30:11	2023-09-14 00:30:11
0ac5f6ae-0916-4b58-b837-56d33b0ad3ea	2a7ec72d-f7f4-44ea-b16b-18d63e3fa9ec	Khu du lịch Ma Thiên Lãnh	khu-du-lich-ma-thien-lanh	Khu du lịch Ma Thiên Lãnh, khu du lich ma thien lanh, khu-du-lich-ma-thien-lanh, khudulichmathienlanh	10	2023-09-14 00:30:11	2023-09-14 00:30:11
2f648fdd-0bb7-4e7f-9ed5-4cabde917c37	2a7ec72d-f7f4-44ea-b16b-18d63e3fa9ec	Hồ Dầu Tiếng	ho-dau-tieng	Hồ Dầu Tiếng, ho dau tieng, ho-dau-tieng, hodautieng	10	2023-09-14 00:30:12	2023-09-14 00:30:12
25d6e454-2ee5-4fed-83f5-57675a5c2c06	2a7ec72d-f7f4-44ea-b16b-18d63e3fa9ec	Căn cứ trung ương cục miền Nam	can-cu-trung-uong-cuc-mien-nam	Căn cứ trung ương cục miền Nam, can cu trung uong cuc mien nam, can-cu-trung-uong-cuc-mien-nam, cancutrunguongcucmiennam	10	2023-09-14 00:30:12	2023-09-14 00:30:12
0ae03d8c-adb0-4a67-a15b-af04db06392e	da7eb4dd-6beb-49fc-ab71-879a8279b644	Khu du lịch thác Giang Điền	khu-du-lich-thac-giang-dien	Khu du lịch thác Giang Điền, khu du lich thac giang dien, khu-du-lich-thac-giang-dien, khudulichthacgiangdien	10	2023-09-14 00:30:12	2023-09-14 00:30:12
d0b2a2d7-afad-4a72-8171-39c264412d06	da7eb4dd-6beb-49fc-ab71-879a8279b644	Thác Mai	thac-mai	Thác Mai, thac mai, thac-mai, thacmai	10	2023-09-14 00:30:12	2023-09-14 00:30:12
452318b7-cd06-4fa6-8fcd-a35fc0c2bd59	da7eb4dd-6beb-49fc-ab71-879a8279b644	Khu du lịch Thác Mơ	khu-du-lich-thac-mo	Khu du lịch Thác Mơ, khu du lich thac mo, khu-du-lich-thac-mo, khudulichthacmo	10	2023-09-14 00:30:12	2023-09-14 00:30:12
fb35eef6-98a5-4f36-b5b5-871eab5f1f89	da7eb4dd-6beb-49fc-ab71-879a8279b644	Khu du lịch Bửu Long	khu-du-lich-buu-long	Khu du lịch Bửu Long, khu du lich buu long, khu-du-lich-buu-long, khudulichbuulong	10	2023-09-14 00:30:13	2023-09-14 00:30:13
72eb81d7-7aea-430d-b829-bd928281d480	da7eb4dd-6beb-49fc-ab71-879a8279b644	Làng du lịch tre Việt Nam	lang-du-lich-tre-viet-nam	Làng du lịch tre Việt Nam, lang du lich tre viet nam, lang-du-lich-tre-viet-nam, langdulichtrevietnam	10	2023-09-14 00:30:13	2023-09-14 00:30:13
53d863d0-903d-4b1b-beaa-f3d22d1c26b0	c9e3afa1-a385-474a-9d23-c8b201afbec5	Côn Đảo	con-dao	Côn Đảo, con dao, con-dao, condao	10	2023-09-14 00:30:13	2023-09-14 00:30:13
67f03c7d-6fb5-459e-ba5a-baee3ecd30eb	c9e3afa1-a385-474a-9d23-c8b201afbec5	Thắng cảnh suối Tiên	thang-canh-suoi-tien	Thắng cảnh suối Tiên, thang canh suoi tien, thang-canh-suoi-tien, thangcanhsuoitien	10	2023-09-14 00:30:13	2023-09-14 00:30:13
577803ab-e24e-4273-8f7f-506679ff00ba	c9e3afa1-a385-474a-9d23-c8b201afbec5	Hòn Bà	hon-ba	Hòn Bà, hon ba, hon-ba, honba	10	2023-09-14 00:30:13	2023-09-14 00:30:13
63370883-dc6a-42ee-8749-28fd78651ab9	c9e3afa1-a385-474a-9d23-c8b201afbec5	Địa đạo Long Phước	dia-dao-long-phuoc	Địa đạo Long Phước, dia dao long phuoc, dia-dao-long-phuoc, diadaolongphuoc	10	2023-09-14 00:30:14	2023-09-14 00:30:14
89536c04-f58a-4142-a8a3-1855a2835092	c9e3afa1-a385-474a-9d23-c8b201afbec5	Hải Đăng Vũng Tàu	hai-dang-vung-tau	Hải Đăng Vũng Tàu, hai dang vung tau, hai-dang-vung-tau, haidangvungtau	10	2023-09-14 00:30:14	2023-09-14 00:30:14
af163286-5f65-476e-bc91-b32e9e1bf2c3	c9e3afa1-a385-474a-9d23-c8b201afbec5	Linh Sơn cổ tự	linh-son-co-tu	Linh Sơn cổ tự, linh son co tu, linh-son-co-tu, linhsoncotu	10	2023-09-14 00:30:14	2023-09-14 00:30:14
f9a14c7d-27b7-4e4f-8bec-655af05fe535	c9e3afa1-a385-474a-9d23-c8b201afbec5	Thích Ca Phật Đài	thich-ca-phat-dai	Thích Ca Phật Đài, thich ca phat dai, thich-ca-phat-dai, thichcaphatdai	10	2023-09-14 00:30:14	2023-09-14 00:30:14
700f69d6-3732-4b99-9f4a-163eff1e7e4f	c9e3afa1-a385-474a-9d23-c8b201afbec5	Bạch Dinh	bach-dinh	Bạch Dinh, bach dinh, bach-dinh, bachdinh	10	2023-09-14 00:30:14	2023-09-14 00:30:14
05ce432d-df73-4a16-aafe-715a23144db6	c9e3afa1-a385-474a-9d23-c8b201afbec5	Rừng nguyên sinh Bình Châu	rung-nguyen-sinh-binh-chau	Rừng nguyên sinh Bình Châu, rung nguyen sinh binh chau, rung-nguyen-sinh-binh-chau, rungnguyensinhbinhchau	10	2023-09-14 00:30:14	2023-09-14 00:30:14
955476e4-f4d1-4cde-a85e-ea8184db137c	c9e3afa1-a385-474a-9d23-c8b201afbec5	Dinh Cổ	dinh-co	Dinh Cổ, dinh co, dinh-co, dinhco	10	2023-09-14 00:30:15	2023-09-14 00:30:15
222ef8ce-2ab1-4608-bd84-e30240b61221	094eb87b-b245-4ff3-be8e-7976bd271034	Khu du lịch sinh thái Cát Tường	khu-du-lich-sinh-thai-cat-tuong	Khu du lịch sinh thái Cát Tường, khu du lich sinh thai cat tuong, khu-du-lich-sinh-thai-cat-tuong, khudulichsinhthaicattuong	10	2023-09-14 00:30:15	2023-09-14 00:30:15
70c8c01c-9b2c-45f3-b305-704691d3df24	094eb87b-b245-4ff3-be8e-7976bd271034	Cảng biển Tân Lập	cang-bien-tan-lap	Cảng biển Tân Lập, cang bien tan lap, cang-bien-tan-lap, cangbientanlap	10	2023-09-14 00:30:15	2023-09-14 00:30:15
b0e10bfb-892f-447a-87fc-054918bb6515	094eb87b-b245-4ff3-be8e-7976bd271034	Làng Sen Việt Nam	lang-sen-viet-nam	Làng Sen Việt Nam, lang sen viet nam, lang-sen-viet-nam, langsenvietnam	10	2023-09-14 00:30:15	2023-09-14 00:30:15
c009976d-838b-43dd-9ad5-e0e648e78b28	094eb87b-b245-4ff3-be8e-7976bd271034	Làng cổ Phước Lộc Thọ	lang-co-phuoc-loc-tho	Làng cổ Phước Lộc Thọ, lang co phuoc loc tho, lang-co-phuoc-loc-tho, langcophuocloctho	10	2023-09-14 00:30:16	2023-09-14 00:30:16
fa5290f5-808f-4ba7-9d2b-853d60cad676	094eb87b-b245-4ff3-be8e-7976bd271034	Khu bảo tồn đất ngập nước Láng Sen	khu-bao-ton-dat-ngap-nuoc-lang-sen	Khu bảo tồn đất ngập nước Láng Sen, khu bao ton dat ngap nuoc lang sen, khu-bao-ton-dat-ngap-nuoc-lang-sen, khubaotondatngapnuoclangsen	10	2023-09-14 00:30:16	2023-09-14 00:30:16
c3f12bf2-ca2c-415b-b3d5-cf13519bfeeb	094eb87b-b245-4ff3-be8e-7976bd271034	Làng nổi Tân Lập	lang-noi-tan-lap	Làng nổi Tân Lập, lang noi tan lap, lang-noi-tan-lap, langnoitanlap	10	2023-09-14 00:30:16	2023-09-14 00:30:16
72d6ef4e-2de1-4d83-844e-7f8295a825f3	15e1d925-ea8f-4e65-9bbd-a1dd34f7191b	Vườn Quốc Gia Tràm Chim	vuon-quoc-gia-tram-chim	Vườn Quốc Gia Tràm Chim, vuon quoc gia tram chim, vuon-quoc-gia-tram-chim, vuonquocgiatramchim	10	2023-09-14 00:30:16	2023-09-14 00:30:16
d7046da8-89a7-4b4f-93b8-68d8a471873a	15e1d925-ea8f-4e65-9bbd-a1dd34f7191b	Khu di tích Xẻo Quýt	khu-di-tich-xeo-quyt	Khu di tích Xẻo Quýt, khu di tich xeo quyt, khu-di-tich-xeo-quyt, khuditichxeoquyt	10	2023-09-14 00:30:16	2023-09-14 00:30:16
3641262a-ba3e-45e0-a4b7-6a21f5a767e2	15e1d925-ea8f-4e65-9bbd-a1dd34f7191b	Khu du lịch sinh thái Gáo Giồng	khu-du-lich-sinh-thai-gao-giong	Khu du lịch sinh thái Gáo Giồng, khu du lich sinh thai gao giong, khu-du-lich-sinh-thai-gao-giong, khudulichsinhthaigaogiong	10	2023-09-14 00:30:17	2023-09-14 00:30:17
57aa61fa-027c-4670-a01c-110db3556ba0	15e1d925-ea8f-4e65-9bbd-a1dd34f7191b	Khu sinh thái Đồng Sen Tháp Mười	khu-sinh-thai-dong-sen-thap-muoi	Khu sinh thái Đồng Sen Tháp Mười, khu sinh thai dong sen thap muoi, khu-sinh-thai-dong-sen-thap-muoi, khusinhthaidongsenthapmuoi	10	2023-09-14 00:30:17	2023-09-14 00:30:17
7a4d30aa-da16-43f1-8ed0-704a814f90df	15e1d925-ea8f-4e65-9bbd-a1dd34f7191b	Nhà cổ Huỳnh Thủy Lê	nha-co-huynh-thuy-le	Nhà cổ Huỳnh Thủy Lê, nha co huynh thuy le, nha-co-huynh-thuy-le, nhacohuynhthuyle	10	2023-09-14 00:30:17	2023-09-14 00:30:17
4cdabc0c-7ff8-432b-9aa6-e4d7eacfea7b	15e1d925-ea8f-4e65-9bbd-a1dd34f7191b	Chùa Phước Kiển	chua-phuoc-kien	Chùa Phước Kiển, chua phuoc kien, chua-phuoc-kien, chuaphuockien	10	2023-09-14 00:30:17	2023-09-14 00:30:17
bd2c4e97-e753-4d68-ba53-cfd6b3fc6bb3	15e1d925-ea8f-4e65-9bbd-a1dd34f7191b	Làng hoa kiểng Sa Đéc	lang-hoa-kieng-sa-dec	Làng hoa kiểng Sa Đéc, lang hoa kieng sa dec, lang-hoa-kieng-sa-dec, langhoakiengsadec	10	2023-09-14 00:30:17	2023-09-14 00:30:17
190f6b59-7d49-45b8-a0d7-6bc1548e547e	89d89c71-8b5e-482e-8e7e-96de0d02cf9f	Chợ nổi cái Bè	cho-noi-cai-be	Chợ nổi cái Bè, cho noi cai be, cho-noi-cai-be, chonoicaibe	10	2023-09-14 00:30:18	2023-09-14 00:30:18
7327d701-1d44-44dd-afc9-3d5a1cd285eb	89d89c71-8b5e-482e-8e7e-96de0d02cf9f	Biển Tâm Thành	bien-tam-thanh	Biển Tâm Thành, bien tam thanh, bien-tam-thanh, bientamthanh	10	2023-09-14 00:30:18	2023-09-14 00:30:18
8436d1b5-996b-4cd6-9eea-5bf8da062813	89d89c71-8b5e-482e-8e7e-96de0d02cf9f	Chùa Vĩnh Tràng	chua-vinh-trang	Chùa Vĩnh Tràng, chua vinh trang, chua-vinh-trang, chuavinhtrang	10	2023-09-14 00:30:18	2023-09-14 00:30:18
d6b67529-bb0f-42b7-9a40-cd02d4cc1968	89d89c71-8b5e-482e-8e7e-96de0d02cf9f	Cù Lao Thới Sơn	cu-lao-thoi-son	Cù Lao Thới Sơn, cu lao thoi son, cu-lao-thoi-son, culaothoison	10	2023-09-14 00:30:18	2023-09-14 00:30:18
4efdb432-4d2f-458e-85f0-a04f3f2834c4	89d89c71-8b5e-482e-8e7e-96de0d02cf9f	Trại rắn Đồng Tâm	trai-ran-dong-tam	Trại rắn Đồng Tâm, trai ran dong tam, trai-ran-dong-tam, trairandongtam	10	2023-09-14 00:30:18	2023-09-14 00:30:18
c28f9f2c-8f88-4936-90aa-94fb70de1301	89d89c71-8b5e-482e-8e7e-96de0d02cf9f	Miệt vườn cái Bè	miet-vuon-cai-be	Miệt vườn cái Bè, miet vuon cai be, miet-vuon-cai-be, mietvuoncaibe	10	2023-09-14 00:30:19	2023-09-14 00:30:19
22a5fd87-e456-4b3f-b017-f93a26e50f14	89d89c71-8b5e-482e-8e7e-96de0d02cf9f	Vườn cây trái Vĩnh Kim	vuon-cay-trai-vinh-kim	Vườn cây trái Vĩnh Kim, vuon cay trai vinh kim, vuon-cay-trai-vinh-kim, vuoncaytraivinhkim	10	2023-09-14 00:30:19	2023-09-14 00:30:19
59f9f3a8-2430-46e6-910f-6354572839ae	89d89c71-8b5e-482e-8e7e-96de0d02cf9f	Cầu Mỹ Thuận	cau-my-thuan	Cầu Mỹ Thuận, cau my thuan, cau-my-thuan, caumythuan	10	2023-09-14 00:30:19	2023-09-14 00:30:19
adae0120-fe1e-4bf0-843e-83ef30f406ce	16c9028f-7dc3-4d38-b617-0796f190e322	Khu du lịch Lan Vương	khu-du-lich-lan-vuong	Khu du lịch Lan Vương, khu du lich lan vuong, khu-du-lich-lan-vuong, khudulichlanvuong	10	2023-09-14 00:30:19	2023-09-14 00:30:19
ca132262-3555-4f93-9d33-3278e0d84dc4	16c9028f-7dc3-4d38-b617-0796f190e322	Khu du lịch làng Bè	khu-du-lich-lang-be	Khu du lịch làng Bè, khu du lich lang be, khu-du-lich-lang-be, khudulichlangbe	10	2023-09-14 00:30:19	2023-09-14 00:30:19
55294b26-b7a7-4dfd-a566-866a08248dba	16c9028f-7dc3-4d38-b617-0796f190e322	Miệt vườn Cái Mơn, chợ Lách	miet-vuon-cai-mon-cho-lach	Miệt vườn Cái Mơn, chợ Lách, miet vuon cai mon, cho lach, miet-vuon-cai-mon-cho-lach, mietvuoncaimoncholach	10	2023-09-14 00:30:20	2023-09-14 00:30:20
5d6cd227-220f-45d1-b85f-9d91cfc6c0c2	16c9028f-7dc3-4d38-b617-0796f190e322	Nông trại du lịch sân chim Vàm Hồ	nong-trai-du-lich-san-chim-vam-ho	Nông trại du lịch sân chim Vàm Hồ, nong trai du lich san chim vam ho, nong-trai-du-lich-san-chim-vam-ho, nongtraidulichsanchimvamho	10	2023-09-14 00:30:20	2023-09-14 00:30:20
6ba7ef72-b65f-4189-b94d-1bd174b7be52	16c9028f-7dc3-4d38-b617-0796f190e322	Khu du lịch Cồn Phụng	khu-du-lich-con-phung	Khu du lịch Cồn Phụng, khu du lich con phung, khu-du-lich-con-phung, khudulichconphung	10	2023-09-14 00:30:20	2023-09-14 00:30:20
3776962e-f478-4426-acf7-ef9a48c0d7dc	16c9028f-7dc3-4d38-b617-0796f190e322	Cồn Quy	con-quy	Cồn Quy, con quy, con-quy, conquy	10	2023-09-14 00:30:20	2023-09-14 00:30:20
dc9d079a-41d4-4266-81b7-93e1e3ec61be	50217795-a63d-4e3e-99ee-24d67249a7b2	Làng nổi Châu Đốc	lang-noi-chau-doc	Làng nổi Châu Đốc, lang noi chau doc, lang-noi-chau-doc, langnoichaudoc	10	2023-09-14 00:30:20	2023-09-14 00:30:20
bf424666-6184-4289-9cfe-beb3c22d3c32	50217795-a63d-4e3e-99ee-24d67249a7b2	Địa điểm Tà Pa	dia-diem-ta-pa	Địa điểm Tà Pa, dia diem ta pa, dia-diem-ta-pa, diadiemtapa	10	2023-09-14 00:30:21	2023-09-14 00:30:21
e5411d6b-f965-407a-803b-769d34abdf49	50217795-a63d-4e3e-99ee-24d67249a7b2	Miếu bà chúa Xứ	mieu-ba-chua-xu	Miếu bà chúa Xứ, mieu ba chua xu, mieu-ba-chua-xu, mieubachuaxu	10	2023-09-14 00:30:21	2023-09-14 00:30:21
4c8f9447-1eb8-41b2-8ecb-0b906477316e	50217795-a63d-4e3e-99ee-24d67249a7b2	Khu Thất Sơn	khu-that-son	Khu Thất Sơn, khu that son, khu-that-son, khuthatson	10	2023-09-14 00:30:21	2023-09-14 00:30:21
14091dfa-1f76-41f2-8109-34b94b4d2515	50217795-a63d-4e3e-99ee-24d67249a7b2	Núi Cô Tô	nui-co-to	Núi Cô Tô, nui co to, nui-co-to, nuicoto	10	2023-09-14 00:30:21	2023-09-14 00:30:21
7066b9d9-9515-49ce-b1fa-329acfa8851b	50217795-a63d-4e3e-99ee-24d67249a7b2	Chợ Tinh Biên	cho-tinh-bien	Chợ Tinh Biên, cho tinh bien, cho-tinh-bien, chotinhbien	10	2023-09-14 00:30:21	2023-09-14 00:30:21
b45b1c45-715d-4bef-b1a5-7b778dd53b9a	50217795-a63d-4e3e-99ee-24d67249a7b2	Búng Bình Thiên	bung-binh-thien	Búng Bình Thiên, bung binh thien, bung-binh-thien, bungbinhthien	10	2023-09-14 00:30:22	2023-09-14 00:30:22
e1b5af7f-2b24-4d1f-aa67-bca0648afba9	50217795-a63d-4e3e-99ee-24d67249a7b2	Rừng Tràm Trà Sư	rung-tram-tra-su	Rừng Tràm Trà Sư, rung tram tra su, rung-tram-tra-su, rungtramtrasu	10	2023-09-14 00:30:22	2023-09-14 00:30:22
8109d64f-a8f4-4da4-8528-640d0f86272c	50217795-a63d-4e3e-99ee-24d67249a7b2	Khu di chỉ Óc Eo	khu-di-chi-oc-eo	Khu di chỉ Óc Eo, khu di chi oc eo, khu-di-chi-oc-eo, khudichioceo	10	2023-09-14 00:30:22	2023-09-14 00:30:22
638ce9d5-0855-4cbb-a51a-8bdf48f98c47	60d00e2c-7544-4f8d-b9c9-352310bab417	Vườn sinh thái Hoa Súng	vuon-sinh-thai-hoa-sung	Vườn sinh thái Hoa Súng, vuon sinh thai hoa sung, vuon-sinh-thai-hoa-sung, vuonsinhthaihoasung	10	2023-09-14 00:30:22	2023-09-14 00:30:22
42882903-c76c-4d84-bfa7-56e005a94448	60d00e2c-7544-4f8d-b9c9-352310bab417	Làng du lịch Mỹ Khánh	lang-du-lich-my-khanh	Làng du lịch Mỹ Khánh, lang du lich my khanh, lang-du-lich-my-khanh, langdulichmykhanh	10	2023-09-14 00:30:22	2023-09-14 00:30:22
cd054b47-09b6-47a7-9a96-4ca96ac235ee	60d00e2c-7544-4f8d-b9c9-352310bab417	Khu du lịch sinh thái Lung Cột Lầu	khu-du-lich-sinh-thai-lung-cot-lau	Khu du lịch sinh thái Lung Cột Lầu, khu du lich sinh thai lung cot lau, khu-du-lich-sinh-thai-lung-cot-lau, khudulichsinhthailungcotlau	10	2023-09-14 00:30:23	2023-09-14 00:30:23
3e3e4233-a541-40cf-b0d0-b6d5b9091d2e	60d00e2c-7544-4f8d-b9c9-352310bab417	Vườn du lịch sinh thái Lê Lộc	vuon-du-lich-sinh-thai-le-loc	Vườn du lịch sinh thái Lê Lộc, vuon du lich sinh thai le loc, vuon-du-lich-sinh-thai-le-loc, vuondulichsinhthaileloc	10	2023-09-14 00:30:23	2023-09-14 00:30:23
32fa594e-a66d-4b47-a959-298094d72c3e	60d00e2c-7544-4f8d-b9c9-352310bab417	Vườn sinh thái Bảo Gia An Viên	vuon-sinh-thai-bao-gia-an-vien	Vườn sinh thái Bảo Gia An Viên, vuon sinh thai bao gia an vien, vuon-sinh-thai-bao-gia-an-vien, vuonsinhthaibaogiaanvien	10	2023-09-14 00:30:23	2023-09-14 00:30:23
04cecdb6-8af0-4e81-9a9b-da8e2511a381	60d00e2c-7544-4f8d-b9c9-352310bab417	Vườn sinh thái Xẻo Nhum	vuon-sinh-thai-xeo-nhum	Vườn sinh thái Xẻo Nhum, vuon sinh thai xeo nhum, vuon-sinh-thai-xeo-nhum, vuonsinhthaixeonhum	10	2023-09-14 00:30:23	2023-09-14 00:30:23
801ef784-e5d9-4a3a-b67b-09da66fd4b2f	60d00e2c-7544-4f8d-b9c9-352310bab417	Cồn Ấu	con-au	Cồn Ấu, con au, con-au, conau	10	2023-09-14 00:30:23	2023-09-14 00:30:23
f6e284f4-d038-402d-aad5-4ffe37bffaa1	60d00e2c-7544-4f8d-b9c9-352310bab417	Khu du lịch Phù Sa	khu-du-lich-phu-sa	Khu du lịch Phù Sa, khu du lich phu sa, khu-du-lich-phu-sa, khudulichphusa	10	2023-09-14 00:30:23	2023-09-14 00:30:23
890a3b09-05f6-4b15-a413-07b421261294	60d00e2c-7544-4f8d-b9c9-352310bab417	Vườn du lịch sinh thái Giáo Dương	vuon-du-lich-sinh-thai-giao-duong	Vườn du lịch sinh thái Giáo Dương, vuon du lich sinh thai giao duong, vuon-du-lich-sinh-thai-giao-duong, vuondulichsinhthaigiaoduong	10	2023-09-14 00:30:24	2023-09-14 00:30:24
bd2317cb-5b5c-48a0-81d2-9548931dd7d8	60d00e2c-7544-4f8d-b9c9-352310bab417	Vườn trái cây 9 Hồng	vuon-trai-cay-9-hong	Vườn trái cây 9 Hồng, vuon trai cay 9 hong, vuon-trai-cay-9-hong, vuontraicay9hong	10	2023-09-14 00:30:24	2023-09-14 00:30:24
bcc73b4f-fb44-4db8-8865-3e3ee05e3ff5	a4e92406-d956-4598-a1ac-4b40b7935fee	Khu du lịch Vinh Sang	khu-du-lich-vinh-sang	Khu du lịch Vinh Sang, khu du lich vinh sang, khu-du-lich-vinh-sang, khudulichvinhsang	10	2023-09-14 00:30:24	2023-09-14 00:30:24
582b69f3-b13d-4106-8f20-a475d8e16e84	a4e92406-d956-4598-a1ac-4b40b7935fee	Vườn Bonsai	vuon-bonsai	Vườn Bonsai, vuon bonsai, vuon-bonsai, vuonbonsai	10	2023-09-14 00:30:24	2023-09-14 00:30:24
2a6828d2-2024-4b0a-a137-ed5b33267631	a4e92406-d956-4598-a1ac-4b40b7935fee	Văn Thánh Miếu	van-thanh-mieu	Văn Thánh Miếu, van thanh mieu, van-thanh-mieu, vanthanhmieu	10	2023-09-14 00:30:25	2023-09-14 00:30:25
f96d1bf5-5a25-41fa-b377-d9200d0b4eaa	a4e92406-d956-4598-a1ac-4b40b7935fee	Khu du lịch Trường An	khu-du-lich-truong-an	Khu du lịch Trường An, khu du lich truong an, khu-du-lich-truong-an, khudulichtruongan	10	2023-09-14 00:30:25	2023-09-14 00:30:25
2ef7fecc-8459-4376-b776-ee0027b4094b	a4e92406-d956-4598-a1ac-4b40b7935fee	Cù Lao An Bình	cu-lao-an-binh	Cù Lao An Bình, cu lao an binh, cu-lao-an-binh, culaoanbinh	10	2023-09-14 00:30:25	2023-09-14 00:30:25
80010f30-6709-4103-aa95-db6f14bf98b2	a4e92406-d956-4598-a1ac-4b40b7935fee	Chùa cổ Long An	chua-co-long-an	Chùa cổ Long An, chua co long an, chua-co-long-an, chuacolongan	10	2023-09-14 00:30:25	2023-09-14 00:30:25
6ba029f4-db88-445c-af2c-edd1ce5039d6	a4e92406-d956-4598-a1ac-4b40b7935fee	Chùa Tiên Châu	chua-tien-chau	Chùa Tiên Châu, chua tien chau, chua-tien-chau, chuatienchau	10	2023-09-14 00:30:25	2023-09-14 00:30:25
e9ecb2b0-3f89-49da-952d-75b26e09712a	9895c0d8-47b0-4d85-8158-90df566bc3b8	Ao Bà Om	ao-ba-om	Ao Bà Om, ao ba om, ao-ba-om, aobaom	10	2023-09-14 00:30:26	2023-09-14 00:30:26
f6edfde6-7edf-46c9-9177-9f0ff79776fc	9895c0d8-47b0-4d85-8158-90df566bc3b8	Bãi biển Ba Động	bai-bien-ba-dong	Bãi biển Ba Động, bai bien ba dong, bai-bien-ba-dong, baibienbadong	10	2023-09-14 00:30:26	2023-09-14 00:30:26
b36e2404-65f2-4e37-9e34-400641f94e64	9895c0d8-47b0-4d85-8158-90df566bc3b8	Cù Lao Long Tri	cu-lao-long-tri	Cù Lao Long Tri, cu lao long tri, cu-lao-long-tri, culaolongtri	10	2023-09-14 00:30:26	2023-09-14 00:30:26
465e62f1-e672-4184-82a4-a63762d13106	9895c0d8-47b0-4d85-8158-90df566bc3b8	Cù Lao Tân Quy	cu-lao-tan-quy	Cù Lao Tân Quy, cu lao tan quy, cu-lao-tan-quy, culaotanquy	10	2023-09-14 00:30:26	2023-09-14 00:30:26
a296b1b3-d023-4bf4-9fde-5ab1cd8da708	9895c0d8-47b0-4d85-8158-90df566bc3b8	Khu du lịch sinh thái Rừng Đước	khu-du-lich-sinh-thai-rung-duoc	Khu du lịch sinh thái Rừng Đước, khu du lich sinh thai rung duoc, khu-du-lich-sinh-thai-rung-duoc, khudulichsinhthairungduoc	10	2023-09-14 00:30:26	2023-09-14 00:30:26
590b83d9-db82-4d9e-89e1-db573d0debac	9895c0d8-47b0-4d85-8158-90df566bc3b8	Bảo Tàng Khmer	bao-tang-khmer	Bảo Tàng Khmer, bao tang khmer, bao-tang-khmer, baotangkhmer	10	2023-09-14 00:30:26	2023-09-14 00:30:26
2dd67954-0c0c-4fa9-89ad-74569254328f	9895c0d8-47b0-4d85-8158-90df566bc3b8	Chùa Vàm Rây	chua-vam-ray	Chùa Vàm Rây, chua vam ray, chua-vam-ray, chuavamray	10	2023-09-14 00:30:27	2023-09-14 00:30:27
42bcccea-ab8f-468b-bea1-8916cccac011	9895c0d8-47b0-4d85-8158-90df566bc3b8	Chùa Cò	chua-co	Chùa Cò, chua co, chua-co, chuaco	10	2023-09-14 00:30:27	2023-09-14 00:30:27
2b5b32a3-dd49-42a1-ae4b-33439e0e6ad6	9895c0d8-47b0-4d85-8158-90df566bc3b8	Chùa Âng	chua-ang	Chùa Âng, chua ang, chua-ang, chuaang	10	2023-09-14 00:30:27	2023-09-14 00:30:27
b5caca82-c061-44b0-b7e3-2cc801e6c672	9895c0d8-47b0-4d85-8158-90df566bc3b8	Chùa Hang	chua-hang	Chùa Hang, chua hang, chua-hang, chuahang	10	2023-09-14 00:30:27	2023-09-14 00:30:27
8ac5baa7-b68e-42ae-b9fb-12814ccf3892	e6794c4a-2b95-4bcf-a2cb-71952bbd04e8	Đảo Phú Quốc	dao-phu-quoc	Đảo Phú Quốc, dao phu quoc, dao-phu-quoc, daophuquoc	10	2023-09-14 00:30:28	2023-09-14 00:30:28
0e606914-cfc2-4c00-9289-0f222e058390	e6794c4a-2b95-4bcf-a2cb-71952bbd04e8	Đảo Nam Du	dao-nam-du	Đảo Nam Du, dao nam du, dao-nam-du, daonamdu	10	2023-09-14 00:30:28	2023-09-14 00:30:28
52c904f3-8015-4509-a5f0-e43fc7c19341	e6794c4a-2b95-4bcf-a2cb-71952bbd04e8	Đảo Bà Lụa	dao-ba-lua	Đảo Bà Lụa, dao ba lua, dao-ba-lua, daobalua	10	2023-09-14 00:30:28	2023-09-14 00:30:28
378641c5-874c-484e-86c4-caa4aaa96c2d	e6794c4a-2b95-4bcf-a2cb-71952bbd04e8	Đảo Hải Tặc	dao-hai-tac	Đảo Hải Tặc, dao hai tac, dao-hai-tac, daohaitac	10	2023-09-14 00:30:28	2023-09-14 00:30:28
fcab30d9-70dc-46c8-bece-e739a551d2d1	e6794c4a-2b95-4bcf-a2cb-71952bbd04e8	Đảo Hòn Sơn	dao-hon-son	Đảo Hòn Sơn, dao hon son, dao-hon-son, daohonson	10	2023-09-14 00:30:28	2023-09-14 00:30:28
21c3ee8d-7e47-4943-b275-eba2c01757a7	e6794c4a-2b95-4bcf-a2cb-71952bbd04e8	Hòn Phụ Tử	hon-phu-tu	Hòn Phụ Tử, hon phu tu, hon-phu-tu, honphutu	10	2023-09-14 00:30:28	2023-09-14 00:30:28
36eb800d-0132-4235-ad75-b24baef79f02	e6794c4a-2b95-4bcf-a2cb-71952bbd04e8	Rạch Giá	rach-gia	Rạch Giá, rach gia, rach-gia, rachgia	10	2023-09-14 00:30:29	2023-09-14 00:30:29
6184ed0d-231d-48f3-b071-127397af9ed4	e6794c4a-2b95-4bcf-a2cb-71952bbd04e8	Khu du lịch rừng U Minh Thượng	khu-du-lich-rung-u-minh-thuong	Khu du lịch rừng U Minh Thượng, khu du lich rung u minh thuong, khu-du-lich-rung-u-minh-thuong, khudulichrunguminhthuong	10	2023-09-14 00:30:29	2023-09-14 00:30:29
62ff734c-c9f2-40c7-b142-518faccdbe84	279eb1ef-ec9a-42df-948b-63166074c647	Chợ nổi Ngã Bảy	cho-noi-nga-bay	Chợ nổi Ngã Bảy, cho noi nga bay, cho-noi-nga-bay, chonoingabay	10	2023-09-14 00:30:29	2023-09-14 00:30:29
b3090ee0-ec48-4d36-8796-7ce4e4188cbe	279eb1ef-ec9a-42df-948b-63166074c647	Khu văn hóa lịch sử Long Mỹ	khu-van-hoa-lich-su-long-my	Khu văn hóa lịch sử Long Mỹ, khu van hoa lich su long my, khu-van-hoa-lich-su-long-my, khuvanhoalichsulongmy	10	2023-09-14 00:30:29	2023-09-14 00:30:29
9e6901d6-c593-4f6e-9ea7-a72a3760b70f	279eb1ef-ec9a-42df-948b-63166074c647	Khu di tích 75	khu-di-tich-75	Khu di tích 75, khu di tich 75, khu-di-tich-75, khuditich75	10	2023-09-14 00:30:29	2023-09-14 00:30:29
1937c872-f401-4660-b3a3-b1cbdaa76582	279eb1ef-ec9a-42df-948b-63166074c647	Công viên Xà No	cong-vien-xa-no	Công viên Xà No, cong vien xa no, cong-vien-xa-no, congvienxano	10	2023-09-14 00:30:30	2023-09-14 00:30:30
d79777cf-7d02-4fff-a429-db89bb52654f	279eb1ef-ec9a-42df-948b-63166074c647	Khu bảo tồn thiên nhiên Lung Ngọc Hoàng	khu-bao-ton-thien-nhien-lung-ngoc-hoang	Khu bảo tồn thiên nhiên Lung Ngọc Hoàng, khu bao ton thien nhien lung ngoc hoang, khu-bao-ton-thien-nhien-lung-ngoc-hoang, khubaotonthiennhienlungngochoang	10	2023-09-14 00:30:30	2023-09-14 00:30:30
cfdde922-f246-4aef-86f3-ef5f0f80f0f7	279eb1ef-ec9a-42df-948b-63166074c647	Căn Cứ Bà Bái	can-cu-ba-bai	Căn Cứ Bà Bái, can cu ba bai, can-cu-ba-bai, cancubabai	10	2023-09-14 00:30:30	2023-09-14 00:30:30
6ca944ce-3f03-4253-8c4a-664e48eff1ce	279eb1ef-ec9a-42df-948b-63166074c647	Khu sinh thái Tây Đô	khu-sinh-thai-tay-do	Khu sinh thái Tây Đô, khu sinh thai tay do, khu-sinh-thai-tay-do, khusinhthaitaydo	10	2023-09-14 00:30:30	2023-09-14 00:30:30
01c4c162-a70e-42ad-8b1c-3bad41ca3aeb	279eb1ef-ec9a-42df-948b-63166074c647	Khu sinh thái Tầm Vu	khu-sinh-thai-tam-vu	Khu sinh thái Tầm Vu, khu sinh thai tam vu, khu-sinh-thai-tam-vu, khusinhthaitamvu	10	2023-09-14 00:30:30	2023-09-14 00:30:30
a53b024a-8ce3-40d7-82a7-5090b75707c9	836c8e04-7d32-4871-b01d-d3f2b0a842f2	Cồn Mỹ Phước	con-my-phuoc	Cồn Mỹ Phước, con my phuoc, con-my-phuoc, conmyphuoc	10	2023-09-14 00:30:31	2023-09-14 00:30:31
98aba40c-169c-430f-988b-a4528cebcb38	836c8e04-7d32-4871-b01d-d3f2b0a842f2	Chùa Chén Kiểu	chua-chen-kieu	Chùa Chén Kiểu, chua chen kieu, chua-chen-kieu, chuachenkieu	10	2023-09-14 00:30:31	2023-09-14 00:30:31
ffc5ef48-1596-4afc-affb-25ce82265ae5	836c8e04-7d32-4871-b01d-d3f2b0a842f2	Chùa đất sét	chua-dat-set	Chùa đất sét, chua dat set, chua-dat-set, chuadatset	10	2023-09-14 00:30:31	2023-09-14 00:30:31
91b6aea2-81d6-4da9-bd18-17d24ce3250f	836c8e04-7d32-4871-b01d-d3f2b0a842f2	Chợ Nổi Ngã Năm	cho-noi-nga-nam	Chợ Nổi Ngã Năm, cho noi nga nam, cho-noi-nga-nam, chonoinganam	10	2023-09-14 00:30:31	2023-09-14 00:30:31
a02dfa43-1e48-46f9-bef7-7ffd0bcd6ac0	836c8e04-7d32-4871-b01d-d3f2b0a842f2	Chùa Dơi	chua-doi	Chùa Dơi, chua doi, chua-doi, chuadoi	10	2023-09-14 00:30:32	2023-09-14 00:30:32
f154f51e-f962-4711-80ab-48bb598c151e	836c8e04-7d32-4871-b01d-d3f2b0a842f2	Vườn cò Tân Long	vuon-co-tan-long	Vườn cò Tân Long, vuon co tan long, vuon-co-tan-long, vuoncotanlong	10	2023-09-14 00:30:32	2023-09-14 00:30:32
30b04d0b-4bb2-429c-a4d0-5650eee7f0f6	d975f203-fca6-45d9-8e94-d763b40adb96	Biển Bạc Liêu	bien-bac-lieu	Biển Bạc Liêu, bien bac lieu, bien-bac-lieu, bienbaclieu	10	2023-09-14 00:30:32	2023-09-14 00:30:32
0ed752b4-c6e1-45aa-a16c-92baa55621f2	d975f203-fca6-45d9-8e94-d763b40adb96	Tháp cổ Vĩnh Hưng	thap-co-vinh-hung	Tháp cổ Vĩnh Hưng, thap co vinh hung, thap-co-vinh-hung, thapcovinhhung	10	2023-09-14 00:30:32	2023-09-14 00:30:32
f9438861-5c2e-4f8a-998c-1f01eb204451	d975f203-fca6-45d9-8e94-d763b40adb96	Sân chim Bạc Liêu	san-chim-bac-lieu	Sân chim Bạc Liêu, san chim bac lieu, san-chim-bac-lieu, sanchimbaclieu	10	2023-09-14 00:30:32	2023-09-14 00:30:32
062f91af-a15d-43d5-a697-f2d4fde2e7ac	d975f203-fca6-45d9-8e94-d763b40adb96	Nhà công tử Bạc Liêu	nha-cong-tu-bac-lieu	Nhà công tử Bạc Liêu, nha cong tu bac lieu, nha-cong-tu-bac-lieu, nhacongtubaclieu	10	2023-09-14 00:30:33	2023-09-14 00:30:33
89b55705-cc05-485d-99e9-95b3b4d3d277	d975f203-fca6-45d9-8e94-d763b40adb96	Nhà thờ Tắc Sậy	nha-tho-tac-say	Nhà thờ Tắc Sậy, nha tho tac say, nha-tho-tac-say, nhathotacsay	10	2023-09-14 00:30:33	2023-09-14 00:30:33
5a946448-56a1-4607-800d-0ab04eaa73b5	d975f203-fca6-45d9-8e94-d763b40adb96	Vườn nhãn cổ	vuon-nhan-co	Vườn nhãn cổ, vuon nhan co, vuon-nhan-co, vuonnhanco	10	2023-09-14 00:30:33	2023-09-14 00:30:33
874f7978-60d4-4121-8af1-867297ff9a82	d975f203-fca6-45d9-8e94-d763b40adb96	Chùa Xiêm Cán	chua-xiem-can	Chùa Xiêm Cán, chua xiem can, chua-xiem-can, chuaxiemcan	10	2023-09-14 00:30:33	2023-09-14 00:30:33
0887147f-8023-41c1-8613-3bda72757e06	d975f203-fca6-45d9-8e94-d763b40adb96	Quần thể kiến trúc nhà tây	quan-the-kien-truc-nha-tay	Quần thể kiến trúc nhà tây, quan the kien truc nha tay, quan-the-kien-truc-nha-tay, quanthekientrucnhatay	10	2023-09-14 00:30:33	2023-09-14 00:30:33
417b0b05-9ff7-486a-9c8c-d7f88bb3b114	d975f203-fca6-45d9-8e94-d763b40adb96	Khu du lịch nhà Mát	khu-du-lich-nha-mat	Khu du lịch nhà Mát, khu du lich nha mat, khu-du-lich-nha-mat, khudulichnhamat	10	2023-09-14 00:30:33	2023-09-14 00:30:33
291180d0-2e72-4285-a2aa-e2364eb0dc84	d975f203-fca6-45d9-8e94-d763b40adb96	Cánh đồng quạt gió Bạc Liêu	canh-dong-quat-gio-bac-lieu	Cánh đồng quạt gió Bạc Liêu, canh dong quat gio bac lieu, canh-dong-quat-gio-bac-lieu, canhdongquatgiobaclieu	10	2023-09-14 00:30:34	2023-09-14 00:30:34
eb808e42-f857-4562-b2bd-bfaf7c233c95	5f4732c8-349f-4ce9-9675-e74a5c79c402	Mũi Cà mau	mui-ca-mau	Mũi Cà mau, mui ca mau, mui-ca-mau, muicamau	10	2023-09-14 00:30:34	2023-09-14 00:30:34
741710cf-a032-4c9c-b643-34be8d31b592	5f4732c8-349f-4ce9-9675-e74a5c79c402	Khu Vườn Chim	khu-vuon-chim	Khu Vườn Chim, khu vuon chim, khu-vuon-chim, khuvuonchim	10	2023-09-14 00:30:34	2023-09-14 00:30:34
78c07c3e-d4da-49f7-8397-36ba84d38ae6	5f4732c8-349f-4ce9-9675-e74a5c79c402	Đảo hòn khoai Cà Mau	dao-hon-khoai-ca-mau	Đảo hòn khoai Cà Mau, dao hon khoai ca mau, dao-hon-khoai-ca-mau, daohonkhoaicamau	10	2023-09-14 00:30:34	2023-09-14 00:30:34
74dddaa2-e398-4b75-9518-8df1a7b666ac	5f4732c8-349f-4ce9-9675-e74a5c79c402	Biển Khai Long	bien-khai-long	Biển Khai Long, bien khai long, bien-khai-long, bienkhailong	10	2023-09-14 00:30:34	2023-09-14 00:30:34
3a5fb3e4-1425-4699-a11c-972c26269489	5f4732c8-349f-4ce9-9675-e74a5c79c402	Rừng U Minh Hạ	rung-u-minh-ha	Rừng U Minh Hạ, rung u minh ha, rung-u-minh-ha, runguminhha	10	2023-09-14 00:30:35	2023-09-14 00:30:35
1e3c84a9-bb63-4382-b377-95c6a91f80e7	5f4732c8-349f-4ce9-9675-e74a5c79c402	Rừng đước Năm Căn	rung-duoc-nam-can	Rừng đước Năm Căn, rung duoc nam can, rung-duoc-nam-can, rungduocnamcan	10	2023-09-14 00:30:35	2023-09-14 00:30:35
d3b279d4-5753-44f2-997a-a329daa9d9e4	5f4732c8-349f-4ce9-9675-e74a5c79c402	Chợ nổi Cà Mau	cho-noi-ca-mau	Chợ nổi Cà Mau, cho noi ca mau, cho-noi-ca-mau, chonoicamau	10	2023-09-14 00:30:35	2023-09-14 00:30:35
776df1bc-553e-4ceb-a9c4-912be7acae25	5f4732c8-349f-4ce9-9675-e74a5c79c402	Vườn quốc gia Mũi Cà Mau	vuon-quoc-gia-mui-ca-mau	Vườn quốc gia Mũi Cà Mau, vuon quoc gia mui ca mau, vuon-quoc-gia-mui-ca-mau, vuonquocgiamuicamau	10	2023-09-14 00:30:35	2023-09-14 00:30:35
73a4716f-98a6-42bf-a106-cf627c88c11a	5f4732c8-349f-4ce9-9675-e74a5c79c402	Hòn Đá Bạc Cà Mau	hon-da-bac-ca-mau	Hòn Đá Bạc Cà Mau, hon da bac ca mau, hon-da-bac-ca-mau, hondabaccamau	10	2023-09-14 00:30:35	2023-09-14 00:30:35
abf3b6f5-f184-4701-b467-1c74abde742d	5f4732c8-349f-4ce9-9675-e74a5c79c402	Đầm Thị Tường	dam-thi-tuong	Đầm Thị Tường, dam thi tuong, dam-thi-tuong, damthituong	10	2023-09-14 00:30:36	2023-09-14 00:30:36
4b7fc38e-2742-4472-a089-b5b072dc82ce	5f4732c8-349f-4ce9-9675-e74a5c79c402	Rừng ngập mặn Cà Mau	rung-ngap-man-ca-mau	Rừng ngập mặn Cà Mau, rung ngap man ca mau, rung-ngap-man-ca-mau, rungngapmancamau	10	2023-09-14 00:30:36	2023-09-14 00:30:36
\.


--
-- Data for Name: posts; Type: TABLE DATA; Schema: public; Owner: trekka
--

COPY public.posts (uuid, author_uuid, dynamic_uuid, parent_uuid, category_uuid, category_map, type, content_type, title, slug, keywords, description, content, featured_image, views, privacy, trashed_status, deleted_at, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: regions; Type: TABLE DATA; Schema: public; Owner: trekka
--

COPY public.regions (uuid, country_uuid, name, slug, code, "position", created_at, updated_at, keywords, priority) FROM stdin;
04d4ba96-d58d-44ff-9cbe-d7bb28cfc244	6c6288ab-2ead-4a97-bf4c-98ca4026b282	Hà Nội	ha-noi	HN	\N	2023-09-14 00:05:43	2023-09-14 00:05:43	Hà Nội, ha noi, ha-noi, hanoi	10
a471fd3a-ad84-4bdc-a993-629f5d28c808	6c6288ab-2ead-4a97-bf4c-98ca4026b282	TP. Hồ Chí Minh	tp-ho-chi-minh	SG	\N	2023-09-14 00:05:43	2023-09-14 00:05:43	TP. Hồ Chí Minh, tp. ho chi minh, tp-ho-chi-minh, tphochiminh	10
60d00e2c-7544-4f8d-b9c9-352310bab417	6c6288ab-2ead-4a97-bf4c-98ca4026b282	Cần Thơ	can-tho	CT	\N	2023-09-14 00:05:43	2023-09-14 00:05:43	Cần Thơ, can tho, can-tho, cantho	10
e173c981-3ac8-4843-b970-3cd4b59d26b8	6c6288ab-2ead-4a97-bf4c-98ca4026b282	Đà Nẵng	da-nang	DN	\N	2023-09-14 00:05:43	2023-09-14 00:05:43	Đà Nẵng, da nang, da-nang, danang	10
c7a6d823-be55-4995-b116-4d098f8f89fd	6c6288ab-2ead-4a97-bf4c-98ca4026b282	Hải Phòng	hai-phong	HP	\N	2023-09-14 00:05:43	2023-09-14 00:05:43	Hải Phòng, hai phong, hai-phong, haiphong	10
50217795-a63d-4e3e-99ee-24d67249a7b2	6c6288ab-2ead-4a97-bf4c-98ca4026b282	An Giang	an-giang	44	\N	2023-09-14 00:05:44	2023-09-14 00:05:44	An Giang, an giang, an-giang, angiang	10
c9e3afa1-a385-474a-9d23-c8b201afbec5	6c6288ab-2ead-4a97-bf4c-98ca4026b282	Bà Rịa - Vũng Tàu	ba-ria-vung-tau	43	\N	2023-09-14 00:05:44	2023-09-14 00:05:44	Bà Rịa - Vũng Tàu, ba ria - vung tau, ba-ria-vung-tau, bariavungtau	10
c2b15328-52af-4787-ac59-641d15d3a7d3	6c6288ab-2ead-4a97-bf4c-98ca4026b282	Bình Dương	binh-duong	57	\N	2023-09-14 00:05:44	2023-09-14 00:05:44	Bình Dương, binh duong, binh-duong, binhduong	10
582dbcb3-5113-4350-9c5a-fa98d9b159b9	6c6288ab-2ead-4a97-bf4c-98ca4026b282	Bình Phước	binh-phuoc	58	\N	2023-09-14 00:05:44	2023-09-14 00:05:44	Bình Phước, binh phuoc, binh-phuoc, binhphuoc	10
a3febc40-c408-4d66-95c1-0983cc0a2e77	6c6288ab-2ead-4a97-bf4c-98ca4026b282	Bình Định	binh-dinh	31	\N	2023-09-14 00:05:44	2023-09-14 00:05:44	Bình Định, binh dinh, binh-dinh, binhdinh	10
08ae3265-5573-4f25-9bb9-9c996898f439	6c6288ab-2ead-4a97-bf4c-98ca4026b282	Bình Thuận	binh-thuan	40	\N	2023-09-14 00:05:44	2023-09-14 00:05:44	Bình Thuận, binh thuan, binh-thuan, binhthuan	10
d975f203-fca6-45d9-8e94-d763b40adb96	6c6288ab-2ead-4a97-bf4c-98ca4026b282	Bạc Liêu	bac-lieu	55	\N	2023-09-14 00:05:45	2023-09-14 00:05:45	Bạc Liêu, bac lieu, bac-lieu, baclieu	10
5fd6643b-ac12-4e23-a4cc-886cb9754f09	6c6288ab-2ead-4a97-bf4c-98ca4026b282	Bắc Giang	bac-giang	54	\N	2023-09-14 00:05:45	2023-09-14 00:05:45	Bắc Giang, bac giang, bac-giang, bacgiang	10
51e03f09-3152-433f-ab48-3512d3643b0d	6c6288ab-2ead-4a97-bf4c-98ca4026b282	Bắc Kạn	bac-kan	53	\N	2023-09-14 00:05:45	2023-09-14 00:05:45	Bắc Kạn, bac kan, bac-kan, backan	10
9569499d-637b-4e44-ac7e-31107ae665ef	6c6288ab-2ead-4a97-bf4c-98ca4026b282	Bắc Ninh	bac-ninh	56	\N	2023-09-14 00:05:45	2023-09-14 00:05:45	Bắc Ninh, bac ninh, bac-ninh, bacninh	10
16c9028f-7dc3-4d38-b617-0796f190e322	6c6288ab-2ead-4a97-bf4c-98ca4026b282	Bến Tre	ben-tre	50	\N	2023-09-14 00:05:45	2023-09-14 00:05:45	Bến Tre, ben tre, ben-tre, bentre	10
44b1fe0e-a95a-4853-8833-7e86638a3b3b	6c6288ab-2ead-4a97-bf4c-98ca4026b282	Cao Bằng	cao-bang	04	\N	2023-09-14 00:05:45	2023-09-14 00:05:45	Cao Bằng, cao bang, cao-bang, caobang	10
5f4732c8-349f-4ce9-9675-e74a5c79c402	6c6288ab-2ead-4a97-bf4c-98ca4026b282	Cà Mau	ca-mau	59	\N	2023-09-14 00:05:46	2023-09-14 00:05:46	Cà Mau, ca mau, ca-mau, camau	10
bf13331c-6373-4ee7-8692-a98b27806ee8	6c6288ab-2ead-4a97-bf4c-98ca4026b282	Đắk Lắk	dak-lak	33	\N	2023-09-14 00:05:46	2023-09-14 00:05:46	Đắk Lắk, dak lak, dak-lak, daklak	10
b378bf39-dcad-428b-a6b3-34665208a4f5	6c6288ab-2ead-4a97-bf4c-98ca4026b282	Đắk Nông	dak-nong	72	\N	2023-09-14 00:05:46	2023-09-14 00:05:46	Đắk Nông, dak nong, dak-nong, daknong	10
554ccc5a-cbb3-4a91-b61c-9e7d5bc8e168	6c6288ab-2ead-4a97-bf4c-98ca4026b282	Điện Biên	dien-bien	71	\N	2023-09-14 00:05:46	2023-09-14 00:05:46	Điện Biên, dien bien, dien-bien, dienbien	10
da7eb4dd-6beb-49fc-ab71-879a8279b644	6c6288ab-2ead-4a97-bf4c-98ca4026b282	Đồng Nai	dong-nai	39	\N	2023-09-14 00:05:46	2023-09-14 00:05:46	Đồng Nai, dong nai, dong-nai, dongnai	10
15e1d925-ea8f-4e65-9bbd-a1dd34f7191b	6c6288ab-2ead-4a97-bf4c-98ca4026b282	Đồng Tháp	dong-thap	45	\N	2023-09-14 00:05:46	2023-09-14 00:05:46	Đồng Tháp, dong thap, dong-thap, dongthap	10
4ae40be9-eb65-4fa2-b8c6-a447c51f2231	6c6288ab-2ead-4a97-bf4c-98ca4026b282	Gia Lai	gia-lai	30	\N	2023-09-14 00:05:47	2023-09-14 00:05:47	Gia Lai, gia lai, gia-lai, gialai	10
85fe3fd6-c084-4651-9862-304d3e05bd0d	6c6288ab-2ead-4a97-bf4c-98ca4026b282	Hà Giang	ha-giang	03	\N	2023-09-14 00:05:47	2023-09-14 00:05:47	Hà Giang, ha giang, ha-giang, hagiang	10
633969d0-05ea-45a9-bf97-83bdca0921a1	6c6288ab-2ead-4a97-bf4c-98ca4026b282	Hà Nam	ha-nam	63	\N	2023-09-14 00:05:47	2023-09-14 00:05:47	Hà Nam, ha nam, ha-nam, hanam	10
e947d435-b016-4482-b04b-c37031bb9032	6c6288ab-2ead-4a97-bf4c-98ca4026b282	Hà Tĩnh	ha-tinh	23	\N	2023-09-14 00:05:47	2023-09-14 00:05:47	Hà Tĩnh, ha tinh, ha-tinh, hatinh	10
5f712f0a-4bf4-4f17-8c6e-3fa1d4efe23a	6c6288ab-2ead-4a97-bf4c-98ca4026b282	Hải Dương	hai-duong	61	\N	2023-09-14 00:05:48	2023-09-14 00:05:48	Hải Dương, hai duong, hai-duong, haiduong	10
279eb1ef-ec9a-42df-948b-63166074c647	6c6288ab-2ead-4a97-bf4c-98ca4026b282	Hậu Giang	hau-giang	73	\N	2023-09-14 00:05:48	2023-09-14 00:05:48	Hậu Giang, hau giang, hau-giang, haugiang	10
35e46bd6-df18-43e6-ae61-7e2a650dbd2e	6c6288ab-2ead-4a97-bf4c-98ca4026b282	Hòa Bình	hoa-binh	14	\N	2023-09-14 00:05:48	2023-09-14 00:05:48	Hòa Bình, hoa binh, hoa-binh, hoabinh	10
7a13bf0c-f16f-416e-b5a0-d24fd16ebc18	6c6288ab-2ead-4a97-bf4c-98ca4026b282	Hưng Yên	hung-yen	66	\N	2023-09-14 00:05:48	2023-09-14 00:05:48	Hưng Yên, hung yen, hung-yen, hungyen	10
1410027e-2963-4933-875f-b4ef12e39375	6c6288ab-2ead-4a97-bf4c-98ca4026b282	Khánh Hòa	khanh-hoa	34	\N	2023-09-14 00:05:48	2023-09-14 00:05:48	Khánh Hòa, khanh hoa, khanh-hoa, khanhhoa	10
e6794c4a-2b95-4bcf-a2cb-71952bbd04e8	6c6288ab-2ead-4a97-bf4c-98ca4026b282	Kiên Giang	kien-giang	47	\N	2023-09-14 00:05:48	2023-09-14 00:05:48	Kiên Giang, kien giang, kien-giang, kiengiang	10
87276da2-a3ea-42b9-ad66-2322b356edd1	6c6288ab-2ead-4a97-bf4c-98ca4026b282	Kon Tum	kon-tum	28	\N	2023-09-14 00:05:49	2023-09-14 00:05:49	Kon Tum, kon tum, kon-tum, kontum	10
3f11ff64-f2d8-40bd-8dcb-25fa3e2fd448	6c6288ab-2ead-4a97-bf4c-98ca4026b282	Lai Châu	lai-chau	01	\N	2023-09-14 00:05:49	2023-09-14 00:05:49	Lai Châu, lai chau, lai-chau, laichau	10
21ea4e6a-bebf-4706-96a4-257144891137	6c6288ab-2ead-4a97-bf4c-98ca4026b282	Lâm Đồng	lam-dong	35	\N	2023-09-14 00:05:49	2023-09-14 00:05:49	Lâm Đồng, lam dong, lam-dong, lamdong	10
20c05720-d47b-46a3-b0e3-b6a5901153c6	6c6288ab-2ead-4a97-bf4c-98ca4026b282	Lạng Sơn	lang-son	09	\N	2023-09-14 00:05:49	2023-09-14 00:05:49	Lạng Sơn, lang son, lang-son, langson	10
87f37a55-07d9-43bd-9fca-7990aea56210	6c6288ab-2ead-4a97-bf4c-98ca4026b282	Lào Cai	lao-cai	02	\N	2023-09-14 00:05:49	2023-09-14 00:05:49	Lào Cai, lao cai, lao-cai, laocai	10
094eb87b-b245-4ff3-be8e-7976bd271034	6c6288ab-2ead-4a97-bf4c-98ca4026b282	Long An	long-an	41	\N	2023-09-14 00:05:50	2023-09-14 00:05:50	Long An, long an, long-an, longan	10
43200db0-f624-418f-9ef0-c0fe3dd1625f	6c6288ab-2ead-4a97-bf4c-98ca4026b282	Nam Định	nam-dinh	67	\N	2023-09-14 00:05:50	2023-09-14 00:05:50	Nam Định, nam dinh, nam-dinh, namdinh	10
5aaa6a5e-e634-4a73-86a5-74b196484609	6c6288ab-2ead-4a97-bf4c-98ca4026b282	Nghệ An	nghe-an	22	\N	2023-09-14 00:05:50	2023-09-14 00:05:50	Nghệ An, nghe an, nghe-an, nghean	10
0fd6d988-5a99-4f8e-ba1f-513bf3f66043	6c6288ab-2ead-4a97-bf4c-98ca4026b282	Ninh Bình	ninh-binh	18	\N	2023-09-14 00:05:50	2023-09-14 00:05:50	Ninh Bình, ninh binh, ninh-binh, ninhbinh	10
c2e1fa4d-be33-4b16-916b-c59118362930	6c6288ab-2ead-4a97-bf4c-98ca4026b282	Ninh Thuận	ninh-thuan	36	\N	2023-09-14 00:05:50	2023-09-14 00:05:50	Ninh Thuận, ninh thuan, ninh-thuan, ninhthuan	10
8c45434f-b696-42e1-bf43-e393a526d824	6c6288ab-2ead-4a97-bf4c-98ca4026b282	Phú Thọ	phu-tho	68	\N	2023-09-14 00:05:50	2023-09-14 00:05:50	Phú Thọ, phu tho, phu-tho, phutho	10
90379627-06fa-4e12-b835-9991b3bc96c7	6c6288ab-2ead-4a97-bf4c-98ca4026b282	Phú Yên	phu-yen	32	\N	2023-09-14 00:05:51	2023-09-14 00:05:51	Phú Yên, phu yen, phu-yen, phuyen	10
a07bdd0b-9f77-4b63-87b3-76e674b0907d	6c6288ab-2ead-4a97-bf4c-98ca4026b282	Quảng Bình	quang-binh	24	\N	2023-09-14 00:05:51	2023-09-14 00:05:51	Quảng Bình, quang binh, quang-binh, quangbinh	10
f059dc67-723a-48db-af08-786a2dee0504	6c6288ab-2ead-4a97-bf4c-98ca4026b282	Quảng Nam	quang-nam	27	\N	2023-09-14 00:05:51	2023-09-14 00:05:51	Quảng Nam, quang nam, quang-nam, quangnam	10
49f50bac-9925-4f1d-8579-2c7bcaf3d3a8	6c6288ab-2ead-4a97-bf4c-98ca4026b282	Quảng Ngãi	quang-ngai	29	\N	2023-09-14 00:05:51	2023-09-14 00:05:51	Quảng Ngãi, quang ngai, quang-ngai, quangngai	10
0c13c812-d0fb-4dd3-9396-4ec45660784b	6c6288ab-2ead-4a97-bf4c-98ca4026b282	Quảng Ninh	quang-ninh	13	\N	2023-09-14 00:05:51	2023-09-14 00:05:51	Quảng Ninh, quang ninh, quang-ninh, quangninh	10
6a6e7a7f-9390-4d95-a893-62dbee4700ab	6c6288ab-2ead-4a97-bf4c-98ca4026b282	Quảng Trị	quang-tri	25	\N	2023-09-14 00:05:52	2023-09-14 00:05:52	Quảng Trị, quang tri, quang-tri, quangtri	10
836c8e04-7d32-4871-b01d-d3f2b0a842f2	6c6288ab-2ead-4a97-bf4c-98ca4026b282	Sóc Trăng	soc-trang	52	\N	2023-09-14 00:05:52	2023-09-14 00:05:52	Sóc Trăng, soc trang, soc-trang, soctrang	10
0681823c-0557-42b2-b8f4-9b3ff5a583fe	6c6288ab-2ead-4a97-bf4c-98ca4026b282	Sơn La	son-la	05	\N	2023-09-14 00:05:52	2023-09-14 00:05:52	Sơn La, son la, son-la, sonla	10
2a7ec72d-f7f4-44ea-b16b-18d63e3fa9ec	6c6288ab-2ead-4a97-bf4c-98ca4026b282	Tây Ninh	tay-ninh	37	\N	2023-09-14 00:05:52	2023-09-14 00:05:52	Tây Ninh, tay ninh, tay-ninh, tayninh	10
8bd99eab-9f6d-4a2b-b7d4-560a90dd775d	6c6288ab-2ead-4a97-bf4c-98ca4026b282	Thái Bình	thai-binh	20	\N	2023-09-14 00:05:52	2023-09-14 00:05:52	Thái Bình, thai binh, thai-binh, thaibinh	10
95a8bd75-3d06-442d-865e-1efa20d55c8b	6c6288ab-2ead-4a97-bf4c-98ca4026b282	Thái Nguyên	thai-nguyen	69	\N	2023-09-14 00:05:52	2023-09-14 00:05:52	Thái Nguyên, thai nguyen, thai-nguyen, thainguyen	10
c71168b4-90e2-42b0-8f3d-10eb5fd44550	6c6288ab-2ead-4a97-bf4c-98ca4026b282	Thanh Hóa	thanh-hoa	21	\N	2023-09-14 00:05:53	2023-09-14 00:05:53	Thanh Hóa, thanh hoa, thanh-hoa, thanhhoa	10
c1ed9437-473b-4627-a853-2f9f85f7e0cb	6c6288ab-2ead-4a97-bf4c-98ca4026b282	Thừa Thiên–Huế	thua-thien-hue	26	\N	2023-09-14 00:05:53	2023-09-14 00:05:53	Thừa Thiên–Huế, thua thien–hue, thua-thien-hue, thuathienhue	10
89d89c71-8b5e-482e-8e7e-96de0d02cf9f	6c6288ab-2ead-4a97-bf4c-98ca4026b282	Tiền Giang	tien-giang	46	\N	2023-09-14 00:05:53	2023-09-14 00:05:53	Tiền Giang, tien giang, tien-giang, tiengiang	10
9895c0d8-47b0-4d85-8158-90df566bc3b8	6c6288ab-2ead-4a97-bf4c-98ca4026b282	Trà Vinh	tra-vinh	51	\N	2023-09-14 00:05:53	2023-09-14 00:05:53	Trà Vinh, tra vinh, tra-vinh, travinh	10
15699e99-ede1-45d5-b766-42f474693397	6c6288ab-2ead-4a97-bf4c-98ca4026b282	Tuyên Quang	tuyen-quang	07	\N	2023-09-14 00:05:53	2023-09-14 00:05:53	Tuyên Quang, tuyen quang, tuyen-quang, tuyenquang	10
a4e92406-d956-4598-a1ac-4b40b7935fee	6c6288ab-2ead-4a97-bf4c-98ca4026b282	Vĩnh Long	vinh-long	49	\N	2023-09-14 00:05:53	2023-09-14 00:05:53	Vĩnh Long, vinh long, vinh-long, vinhlong	10
7fd0ed8f-97b7-414d-b2d2-d87a9369f09d	6c6288ab-2ead-4a97-bf4c-98ca4026b282	Vĩnh Phúc	vinh-phuc	70	\N	2023-09-14 00:05:54	2023-09-14 00:05:54	Vĩnh Phúc, vinh phuc, vinh-phuc, vinhphuc	10
5f8fd043-51de-472a-8b07-1292c906ee0f	6c6288ab-2ead-4a97-bf4c-98ca4026b282	Yên Bái	yen-bai	06	\N	2023-09-14 00:05:54	2023-09-14 00:05:54	Yên Bái, yen bai, yen-bai, yenbai	10
\.


--
-- Data for Name: report_logs; Type: TABLE DATA; Schema: public; Owner: trekka
--

COPY public.report_logs (uuid, success, fail, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: require_partner_hobbies; Type: TABLE DATA; Schema: public; Owner: trekka
--

COPY public.require_partner_hobbies (uuid, user_uuid, require_uuid, hobby_uuid, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: tag_refs; Type: TABLE DATA; Schema: public; Owner: trekka
--

COPY public.tag_refs (uuid, tag_uuid, ref, ref_uuid, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: tags; Type: TABLE DATA; Schema: public; Owner: trekka
--

COPY public.tags (uuid, name, name_lower, keyword, slug, tagged_count, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: trip_points; Type: TABLE DATA; Schema: public; Owner: trekka
--

COPY public.trip_points (uuid, user_uuid, require_uuid, place_uuid, type, title, description, from_date, to_date, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: user_hobbies; Type: TABLE DATA; Schema: public; Owner: trekka
--

COPY public.user_hobbies (uuid, user_uuid, hobby_uuid, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: user_notices; Type: TABLE DATA; Schema: public; Owner: trekka
--

COPY public.user_notices (uuid, user_uuid, notice_uuid, seen, seen_at, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: users; Type: TABLE DATA; Schema: public; Owner: trekka
--

COPY public.users (uuid, full_name, gender, birthday, email, username, password, phone, avatar, type, affiliate_code, ref_code, wallet_balance, agent_discount, connect_count, country_code, locale, mbti, trust_score, bio, region_uuid, district_uuid, ward_uuid, address, identity_card_id, is_verified_phone, is_verified_email, is_verified_identity, status, google2fa_secret, email_verified_at, remember_token, trashed_status, deleted_at, created_at, updated_at, agent_expired_at) FROM stdin;
802e09cb-d7de-4621-95e4-69a56436ad15	Phùng Đức Thắng	MALE	1998-09-12	thangphung.work@gmail.com	thangphungwork	$2y$10$uK1wUPVx/tgAmJS.tVK3lefBjsV7iLx2FSORM6ZH4VcGCO87TO7u6	0382245838	\N	admin	B7D66C	\N	0	0	0	VN	vi	\N	0	\N	\N	\N	\N	\N	\N	f	f	f	1	\N	\N	\N	0	\N	2023-09-10 22:59:18	2023-09-17 14:32:55	\N
5ab97801-7ce3-477d-92f9-720b20fbb958	Lê Ngọc Doãn	MALE	1992-11-16	doanln16@gmail.com	doanln16	$2y$10$IwUnLLn3xQeyturDzLgaIee1rLnvOulSyb.4Vz87.CQvENV.8uGM.	0945786960	\N	admin	31C63C	\N	0	0	0	VN	vi	\N	0	\N	\N	\N	\N	172, đường Bà Triệu, p. Dân Chủ, tp. Hòa Bình	\N	f	f	f	1	\N	\N	MDvaVmkHKjmGBu7eXBlHdJzXDV2y1VGvYYHMxGoV5L5lMYEZmELycClke7f0	0	\N	2023-09-07 11:53:11	2023-09-17 13:52:07	\N
e1ec17f5-5826-4be2-8ef0-d66f52fc9799	Ben Lindgren	OTHER	1996-01-01	macejkovic.gwen@example.com	afeeney	$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi	\N	\N	user	\N	\N	0	0	0	VN	vi	\N	0	\N	\N	\N	\N	\N	\N	f	f	f	1	\N	2023-09-15 11:18:26	vuHP8JGFqC	0	\N	2023-09-15 11:18:27	2023-09-15 11:18:27	\N
7f6ab7df-e87d-4989-9025-68625bb3dde6	Rolando O'Kon	FEMALE	1992-01-01	rodrigo.becker@example.net	milton22	$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi	\N	\N	user	\N	\N	0	0	0	VN	vi	\N	0	\N	\N	\N	\N	\N	\N	f	f	f	1	\N	2023-09-15 11:18:26	5SdA0JsiZH	0	\N	2023-09-15 11:18:27	2023-09-15 11:18:27	\N
c93acbb4-19b7-41a1-8614-952bb9f0f2d8	Dejah Kilback	OTHER	1962-01-01	ollie.satterfield@example.org	heather31	$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi	\N	\N	user	\N	\N	0	0	0	VN	vi	\N	0	\N	\N	\N	\N	\N	\N	f	f	f	1	\N	2023-09-15 11:18:26	ovnRqYkjcD	0	\N	2023-09-15 11:18:27	2023-09-15 11:18:27	\N
aca7fe13-994b-497f-8cb9-52ec20992124	Reyna Greenholt	OTHER	1962-01-01	ollie71@example.com	eliane.rodriguez	$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi	\N	\N	user	\N	\N	0	0	0	VN	vi	\N	0	\N	\N	\N	\N	\N	\N	f	f	f	1	\N	2023-09-15 11:18:26	eqppEHinOb	0	\N	2023-09-15 11:18:27	2023-09-15 11:18:27	\N
4b1cec5e-f11e-48d6-9238-22e32f76ff8d	Lloyd Smitham	FEMALE	1997-01-01	magnus.prosacco@example.net	dickinson.ernesto	$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi	\N	\N	user	\N	\N	0	0	0	VN	vi	\N	0	\N	\N	\N	\N	\N	\N	f	f	f	1	\N	2023-09-15 11:18:26	50pedgLhcF	0	\N	2023-09-15 11:18:27	2023-09-15 11:18:27	\N
696ed4ee-f6df-4e67-b067-741394ef70d4	Deborah Krajcik II	OTHER	1977-01-01	floyd96@example.net	schaden.gerry	$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi	\N	\N	user	\N	\N	0	0	0	VN	vi	\N	0	\N	\N	\N	\N	\N	\N	f	f	f	1	\N	2023-09-15 11:18:30	fwkUCKgyW0	0	\N	2023-09-15 11:18:30	2023-09-15 11:18:30	\N
5fa67f8a-e983-4c22-9a22-8d480f572e83	Ms. Aylin Schmidt Sr.	OTHER	1960-01-01	salvatore71@example.com	dora92	$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi	\N	\N	user	\N	\N	0	0	0	VN	vi	\N	0	\N	\N	\N	\N	\N	\N	f	f	f	1	\N	2023-09-15 11:18:30	QgMFDqaCTH	0	\N	2023-09-15 11:18:31	2023-09-15 11:18:31	\N
c7c0b44c-8ffe-4ed6-9637-bd262e22c7d3	Mr. Jovani Olson Sr.	FEMALE	2004-01-01	brandt94@example.net	schumm.janie	$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi	\N	\N	user	\N	\N	0	0	0	VN	vi	\N	0	\N	\N	\N	\N	\N	\N	f	f	f	1	\N	2023-09-15 11:18:30	pE44bCOQmh	0	\N	2023-09-15 11:18:31	2023-09-15 11:18:31	\N
52915ea3-6b7c-46c5-b0bd-338cd745afea	Caterina Effertz	OTHER	2007-01-01	mkozey@example.com	mwilderman	$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi	\N	\N	user	\N	\N	0	0	0	VN	vi	\N	0	\N	\N	\N	\N	\N	\N	f	f	f	1	\N	2023-09-15 11:18:30	oj8A0Fj2Jk	0	\N	2023-09-15 11:18:31	2023-09-15 11:18:31	\N
abcdee76-a1d1-40dd-b2fb-cb03b6e97355	Elena Aufderhar	OTHER	2006-01-01	ernser.kyra@example.net	kozey.jo	$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi	\N	\N	user	\N	\N	0	0	0	VN	vi	\N	0	\N	\N	\N	\N	\N	\N	f	f	f	1	\N	2023-09-15 11:18:30	IVOZgSYRbY	0	\N	2023-09-15 11:18:31	2023-09-15 11:18:31	\N
16d80903-7d01-45c1-8a33-01496ca22b63	Mr. Mathias Waelchi PhD	FEMALE	1994-01-01	pollich.jessy@example.org	robel.cecelia	$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi	\N	\N	user	\N	\N	0	0	0	VN	vi	\N	0	\N	\N	\N	\N	\N	\N	f	f	f	1	\N	2023-09-15 11:18:33	ycKFd7TP8v	0	\N	2023-09-15 11:18:33	2023-09-15 11:18:33	\N
d08a8648-cfff-492a-84cf-d95eeea78ad7	Emelie Schiller	MALE	1998-01-01	will.sigurd@example.com	ellis.hartmann	$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi	\N	\N	user	\N	\N	0	0	0	VN	vi	\N	0	\N	\N	\N	\N	\N	\N	f	f	f	1	\N	2023-09-15 11:18:33	3SDJksgAWa	0	\N	2023-09-15 11:18:33	2023-09-15 11:18:33	\N
ddf74034-d166-4563-9301-4f8ccd84e28b	Colt Bartoletti III	MALE	1987-01-01	mwiza@example.net	vivianne91	$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi	\N	\N	user	\N	\N	0	0	0	VN	vi	\N	0	\N	\N	\N	\N	\N	\N	f	f	f	1	\N	2023-09-15 11:18:33	7njqLWjcEH	0	\N	2023-09-15 11:18:33	2023-09-15 11:18:33	\N
7c6af492-c1c1-4e4f-8b1e-e63c98b0734a	Dimitri Haley	OTHER	1969-01-01	emmanuelle72@example.org	shemar87	$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi	\N	\N	user	\N	\N	0	0	0	VN	vi	\N	0	\N	\N	\N	\N	\N	\N	f	f	f	1	\N	2023-09-15 11:18:33	CJMci2HbQl	0	\N	2023-09-15 11:18:33	2023-09-15 11:18:33	\N
d102e230-7e98-4325-aa46-55b6574f2f62	Kayden Stark DVM	FEMALE	1965-01-01	carolanne.crist@example.org	okeefe.maude	$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi	\N	\N	user	\N	\N	0	0	0	VN	vi	\N	0	\N	\N	\N	\N	\N	\N	f	f	f	1	\N	2023-09-15 11:18:33	kbGP851RUo	0	\N	2023-09-15 11:18:33	2023-09-15 11:18:33	\N
2b19e6f3-bf24-48c6-af00-52c15cc76bde	Joanie Jakubowski	OTHER	1987-01-01	maggie.huels@example.com	djohns	$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi	\N	\N	user	\N	\N	0	0	0	VN	vi	\N	0	\N	\N	\N	\N	\N	\N	f	f	f	1	\N	2023-09-15 11:21:47	mri0YNjKV9	0	\N	2023-09-15 11:21:47	2023-09-15 11:21:47	\N
e3af23c0-dfd3-4b15-a7ec-c2a752f2a581	Ms. Frieda Swift	FEMALE	2006-01-01	lizeth60@example.net	kweber	$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi	\N	\N	user	\N	\N	0	0	0	VN	vi	\N	0	\N	\N	\N	\N	\N	\N	f	f	f	1	\N	2023-09-15 11:21:47	0yryI0YMCJ	0	\N	2023-09-15 11:21:47	2023-09-15 11:21:47	\N
545bc196-1795-48fb-9c2c-3e55b3e6a2a2	Kris Baumbach	FEMALE	1983-01-01	audrey58@example.com	fkohler	$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi	\N	\N	user	\N	\N	0	0	0	VN	vi	\N	0	\N	\N	\N	\N	\N	\N	f	f	f	1	\N	2023-09-15 11:21:47	YxUnWvZLtW	0	\N	2023-09-15 11:21:47	2023-09-15 11:21:47	\N
61a969c4-9325-432e-9bf2-763e3db5fe7d	Maritza Armstrong	OTHER	1967-01-01	kristy.ward@example.net	stuart04	$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi	\N	\N	user	\N	\N	0	0	0	VN	vi	\N	0	\N	\N	\N	\N	\N	\N	f	f	f	1	\N	2023-09-15 11:21:47	y0yYEkoR4M	0	\N	2023-09-15 11:21:47	2023-09-15 11:21:47	\N
ceca2b70-4c2e-4204-ba2a-234c5574a209	Clair Hilpert	MALE	1960-01-01	mvonrueden@example.org	heathcote.jordyn	$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi	\N	\N	user	\N	\N	0	0	0	VN	vi	\N	0	\N	\N	\N	\N	\N	\N	f	f	f	1	\N	2023-09-15 11:21:47	Bks1zGsXWs	0	\N	2023-09-15 11:21:47	2023-09-15 11:21:47	\N
d76cae1c-551a-4c87-b260-4649c7a7765a	Prof. Jane Paucek III	MALE	1989-01-01	opollich@example.org	sschiller	$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi	\N	\N	user	\N	\N	0	0	0	VN	vi	\N	0	\N	\N	\N	\N	\N	\N	f	f	f	1	\N	2023-09-15 11:21:47	VvydFSYksd	0	\N	2023-09-15 11:21:48	2023-09-15 11:21:48	\N
389b9311-80ff-4e4b-8b9f-8d34612bed78	Prof. Jessyca Smith	FEMALE	1971-01-01	matt03@example.net	fahey.catalina	$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi	\N	\N	user	\N	\N	0	0	0	VN	vi	\N	0	\N	\N	\N	\N	\N	\N	f	f	f	1	\N	2023-09-15 11:21:47	UNwLYynovw	0	\N	2023-09-15 11:21:48	2023-09-15 11:21:48	\N
af19001d-a302-4733-b08e-93b95686e307	Ahmed Stracke	FEMALE	1975-01-01	cartwright.april@example.net	jerde.aurore	$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi	\N	\N	user	\N	\N	0	0	0	VN	vi	\N	0	\N	\N	\N	\N	\N	\N	f	f	f	1	\N	2023-09-15 11:21:47	LIQxfnhtel	0	\N	2023-09-15 11:21:48	2023-09-15 11:21:48	\N
3371fe17-56e2-4587-8dda-e728d52b6bd6	Winifred Metz	OTHER	1969-01-01	phyllis19@example.com	rhowell	$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi	\N	\N	user	\N	\N	0	0	0	VN	vi	\N	0	\N	\N	\N	\N	\N	\N	f	f	f	1	\N	2023-09-15 11:21:47	Y5UqUH0bT3	0	\N	2023-09-15 11:21:48	2023-09-15 11:21:48	\N
de058064-9294-4d94-9834-d2dfa8e9ea43	Kelli Wisoky	OTHER	1960-01-01	keanu24@example.net	bernita31	$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi	\N	\N	user	\N	\N	0	0	0	VN	vi	\N	0	\N	\N	\N	\N	\N	\N	f	f	f	1	\N	2023-09-15 11:21:47	TTMFydW6WA	0	\N	2023-09-15 11:21:48	2023-09-15 11:21:48	\N
553f6965-2f1f-45a8-bc47-6fc099b64e87	Gus Zieme II	OTHER	1969-01-01	jaydon01@example.com	valentina.anderson	$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi	\N	\N	user	\N	\N	0	0	0	VN	vi	\N	0	\N	\N	\N	\N	\N	\N	f	f	f	1	\N	2023-09-15 11:21:47	xIeArN3XoH	0	\N	2023-09-15 11:21:48	2023-09-15 11:21:48	\N
5c5b1d29-b2cc-4b92-bd5f-36d0521c77ac	Chaz Mills	FEMALE	1971-01-01	gbreitenberg@example.net	garett07	$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi	\N	\N	user	\N	\N	0	0	0	VN	vi	\N	0	\N	\N	\N	\N	\N	\N	f	f	f	1	\N	2023-09-15 11:21:47	3DxLHjgll1	0	\N	2023-09-15 11:21:48	2023-09-15 11:21:48	\N
23f345c1-4a07-409a-92aa-0887fababce5	Gail Macejkovic DDS	OTHER	1965-01-01	bridget27@example.com	qmacejkovic	$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi	\N	\N	user	\N	\N	0	0	0	VN	vi	\N	0	\N	\N	\N	\N	\N	\N	f	f	f	1	\N	2023-09-15 11:21:47	BtuJ6drP3Y	0	\N	2023-09-15 11:21:48	2023-09-15 11:21:48	\N
2fb462a4-45a4-48fe-92da-6603efbe72b7	Dr. Orpha Smith Sr.	OTHER	2003-01-01	mozell09@example.com	kreiger.cristobal	$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi	\N	\N	user	\N	\N	0	0	0	VN	vi	\N	0	\N	\N	\N	\N	\N	\N	f	f	f	1	\N	2023-09-15 11:21:47	NTx5QzhBmn	0	\N	2023-09-15 11:21:48	2023-09-15 11:21:48	\N
21ad8a61-3762-4a9f-9050-265ef1d9ad0f	Marlee Boehm	FEMALE	1963-01-01	lucio62@example.com	polly.mcdermott	$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi	\N	\N	user	\N	\N	0	0	0	VN	vi	\N	0	\N	\N	\N	\N	\N	\N	f	f	f	1	\N	2023-09-15 11:21:47	ZKMyS0SwjI	0	\N	2023-09-15 11:21:48	2023-09-15 11:21:48	\N
e82aeaaf-ab51-4aea-b2bc-1abfa03b1f25	Jerome Blanda Sr.	OTHER	2004-01-01	orion.veum@example.net	metz.dasia	$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi	\N	\N	user	\N	\N	0	0	0	VN	vi	\N	0	\N	\N	\N	\N	\N	\N	f	f	f	1	\N	2023-09-15 11:21:47	urPinSh70z	0	\N	2023-09-15 11:21:48	2023-09-15 11:21:48	\N
717dc3c0-0432-4cdc-8563-2898b6e2f1b2	Amber Dibbert	MALE	1986-01-01	wiza.cheyanne@example.org	tyrel.carter	$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi	\N	\N	user	\N	\N	0	0	0	VN	vi	\N	0	\N	\N	\N	\N	\N	\N	f	f	f	1	\N	2023-09-15 11:21:47	NZpR54bLmZ	0	\N	2023-09-15 11:21:48	2023-09-15 11:21:48	\N
9f470669-ef2b-4dde-b424-0435a122ea0a	Mr. Owen Kovacek	OTHER	2004-01-01	dudley.morar@example.org	araceli08	$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi	\N	\N	user	\N	\N	0	0	0	VN	vi	\N	0	\N	\N	\N	\N	\N	\N	f	f	f	1	\N	2023-09-15 11:21:47	QT2KO2W7R8	0	\N	2023-09-15 11:21:49	2023-09-15 11:21:49	\N
a247238f-aa4c-497c-9faa-26320d87580f	Devon Ziemann	OTHER	1985-01-01	patsy32@example.org	qrosenbaum	$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi	\N	\N	user	\N	\N	0	0	0	VN	vi	\N	0	\N	\N	\N	\N	\N	\N	f	f	f	1	\N	2023-09-15 11:21:47	0rbFYfot2q	0	\N	2023-09-15 11:21:49	2023-09-15 11:21:49	\N
f34f4bcb-6ed6-4922-8872-d53598593aed	Abner Barton Sr.	OTHER	2004-01-01	qfahey@example.org	corbin15	$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi	\N	\N	user	\N	\N	0	0	0	VN	vi	\N	0	\N	\N	\N	\N	\N	\N	f	f	f	1	\N	2023-09-15 11:21:47	Dhy0V1uE3A	0	\N	2023-09-15 11:21:49	2023-09-15 11:21:49	\N
dfe1397a-fd81-4bec-8dc6-fc11f0fe2671	Monty Bogisich	OTHER	1997-01-01	xchamplin@example.com	haley.willa	$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi	\N	\N	user	\N	\N	0	0	0	VN	vi	\N	0	\N	\N	\N	\N	\N	\N	f	f	f	1	\N	2023-09-15 11:21:47	a9N1uaGVgp	0	\N	2023-09-15 11:21:49	2023-09-15 11:21:49	\N
fd5faec3-7953-4cbc-bc80-3835d63520bd	Eduardo Rodriguez DVM	FEMALE	1965-01-01	vbraun@example.org	jazmyne.doyle	$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi	\N	\N	user	\N	\N	0	0	0	VN	vi	\N	0	\N	\N	\N	\N	\N	\N	f	f	f	1	\N	2023-09-15 11:21:47	9KWxQXz3Db	0	\N	2023-09-15 11:21:49	2023-09-15 11:21:49	\N
2a6aef61-abc3-44a7-98d1-e5c1c47058a3	Keon Towne	OTHER	1997-01-01	jsauer@example.net	feeney.marisol	$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi	\N	\N	user	\N	\N	0	0	0	VN	vi	\N	0	\N	\N	\N	\N	\N	\N	f	f	f	1	\N	2023-09-15 11:21:47	NTfGHV7yzR	0	\N	2023-09-15 11:21:49	2023-09-15 11:21:49	\N
f9dfda3f-443c-4ae5-bf40-312926bed590	Mrs. Julie Kunde V	FEMALE	1961-01-01	ervin61@example.org	glenna.grady	$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi	\N	\N	user	\N	\N	0	0	0	VN	vi	\N	0	\N	\N	\N	\N	\N	\N	f	f	f	1	\N	2023-09-15 11:21:47	LAUJ0Pjtxv	0	\N	2023-09-15 11:21:49	2023-09-15 11:21:49	\N
0a9a7c61-702d-4e90-8e04-26c5dd2f3624	Bryon Collier	OTHER	1986-01-01	jena28@example.com	wbins	$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi	\N	\N	user	\N	\N	0	0	0	VN	vi	\N	0	\N	\N	\N	\N	\N	\N	f	f	f	1	\N	2023-09-15 11:21:47	3oRqAHxC5a	0	\N	2023-09-15 11:21:49	2023-09-15 11:21:49	\N
ec7c377d-7164-4dc1-8b4f-a627cb06fde1	Felicita Schimmel DVM	OTHER	1973-01-01	klockman@example.org	gutkowski.fanny	$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi	\N	\N	user	\N	\N	0	0	0	VN	vi	\N	0	\N	\N	\N	\N	\N	\N	f	f	f	1	\N	2023-09-15 11:21:47	7pG7HATUTv	0	\N	2023-09-15 11:21:49	2023-09-15 11:21:49	\N
ed0250b7-6b9c-4537-85fa-4f0526efc740	Milton Schultz	MALE	1979-01-01	nona.bruen@example.com	geovanny.davis	$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi	\N	\N	user	\N	\N	0	0	0	VN	vi	\N	0	\N	\N	\N	\N	\N	\N	f	f	f	1	\N	2023-09-15 11:21:47	fZkPp786hb	0	\N	2023-09-15 11:21:49	2023-09-15 11:21:49	\N
b0d453da-f96a-4798-9e4e-98da4b01b7d5	Breanna Weissnat	OTHER	1992-01-01	bartoletti.alvis@example.com	kreiger.tate	$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi	\N	\N	user	\N	\N	0	0	0	VN	vi	\N	0	\N	\N	\N	\N	\N	\N	f	f	f	1	\N	2023-09-15 11:21:47	OrXAs0ttmj	0	\N	2023-09-15 11:21:49	2023-09-15 11:21:49	\N
8f76c8ba-98fb-4b59-af2c-8b5679c3644d	Claire Windler	OTHER	2001-01-01	steuber.aubrey@example.org	natasha.ratke	$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi	\N	\N	user	\N	\N	0	0	0	VN	vi	\N	0	\N	\N	\N	\N	\N	\N	f	f	f	1	\N	2023-09-15 11:21:47	xzdUfLcqaT	0	\N	2023-09-15 11:21:50	2023-09-15 11:21:50	\N
c84409b7-5948-409c-bd16-baa04b23ff88	Prof. Modesta Hayes	MALE	1964-01-01	ardith.predovic@example.net	pauline52	$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi	\N	\N	user	\N	\N	0	0	0	VN	vi	\N	0	\N	\N	\N	\N	\N	\N	f	f	f	1	\N	2023-09-15 11:21:47	ClO57TWqhD	0	\N	2023-09-15 11:21:50	2023-09-15 11:21:50	\N
002bb462-75c7-42eb-8677-87ea8e921c62	Daren Metz	OTHER	1996-01-01	gmitchell@example.net	jreichert	$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi	\N	\N	user	\N	\N	0	0	0	VN	vi	\N	0	\N	\N	\N	\N	\N	\N	f	f	f	1	\N	2023-09-15 11:21:47	57KAx980YF	0	\N	2023-09-15 11:21:50	2023-09-15 11:21:50	\N
d6826726-4b86-4f1e-b656-4e84b84f077b	Prof. Keyshawn Wilderman	FEMALE	1984-01-01	bprosacco@example.com	nickolas.beahan	$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi	\N	\N	user	\N	\N	0	0	0	VN	vi	\N	0	\N	\N	\N	\N	\N	\N	f	f	f	1	\N	2023-09-15 11:21:47	aWrfAFzjmo	0	\N	2023-09-15 11:21:50	2023-09-15 11:21:50	\N
3369dc9d-eced-4c73-b055-c2a10299413b	Carissa Muller	OTHER	1970-01-01	weber.alexane@example.org	charlie62	$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi	\N	\N	user	\N	\N	0	0	0	VN	vi	\N	0	\N	\N	\N	\N	\N	\N	f	f	f	1	\N	2023-09-15 11:21:47	BQh1ChyLRw	0	\N	2023-09-15 11:21:50	2023-09-15 11:21:50	\N
7e35c37b-41a2-4dbe-890a-2a19244444cf	Roosevelt Kris DDS	OTHER	1964-01-01	davonte.daniel@example.net	blanca94	$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi	\N	\N	user	\N	\N	0	0	0	VN	vi	\N	0	\N	\N	\N	\N	\N	\N	f	f	f	1	\N	2023-09-15 11:21:47	GmCoRBK087	0	\N	2023-09-15 11:21:50	2023-09-15 11:21:50	\N
2f0234ff-3c1a-4fd1-aadd-4553e52a387a	Derick Koch	FEMALE	2005-01-01	demarcus89@example.org	ricky97	$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi	\N	\N	user	\N	\N	0	0	0	VN	vi	\N	0	\N	\N	\N	\N	\N	\N	f	f	f	1	\N	2023-09-15 11:21:47	AyoHfhRvW7	0	\N	2023-09-15 11:21:50	2023-09-15 11:21:50	\N
deec4a47-5e1b-4fbe-9b01-9a8c6530b6ba	Earline Barton	MALE	1961-01-01	nelda41@example.net	camille.kuhlman	$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi	\N	\N	user	\N	\N	0	0	0	VN	vi	\N	0	\N	\N	\N	\N	\N	\N	f	f	f	1	\N	2023-09-15 11:21:47	JfYIXqYe2r	0	\N	2023-09-15 11:21:50	2023-09-15 11:21:50	\N
4c2222bb-6c2d-4307-9e10-cc09254bccdd	Kaden Spencer DDS	FEMALE	1997-01-01	blanda.gretchen@example.org	asha.terry	$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi	\N	\N	user	\N	\N	0	0	0	VN	vi	\N	0	\N	\N	\N	\N	\N	\N	f	f	f	1	\N	2023-09-15 11:21:47	YdH2Qqz4Ni	0	\N	2023-09-15 11:21:50	2023-09-15 11:21:50	\N
4d01eea9-0fd0-4868-a28e-228cfa890291	Ms. Enola Abbott IV	OTHER	1975-01-01	jaida.weissnat@example.com	lreichert	$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi	\N	\N	user	\N	\N	0	0	0	VN	vi	\N	0	\N	\N	\N	\N	\N	\N	f	f	f	1	\N	2023-09-15 11:21:47	fr8cgJhjlh	0	\N	2023-09-15 11:21:50	2023-09-15 11:21:50	\N
eba7e524-5d75-4f6c-aecd-77350cd9724e	Imani Ferry	FEMALE	2004-01-01	koch.hans@example.com	dwhite	$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi	\N	\N	user	\N	\N	0	0	0	VN	vi	\N	0	\N	\N	\N	\N	\N	\N	f	f	f	1	\N	2023-09-15 11:21:47	72kbl9E9Ed	0	\N	2023-09-15 11:21:50	2023-09-15 11:21:50	\N
61028bae-8473-407f-a7ae-25a163387e38	Colton Rempel	MALE	1983-01-01	stark.jacynthe@example.org	parker.gerlach	$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi	\N	\N	user	\N	\N	0	0	0	VN	vi	\N	0	\N	\N	\N	\N	\N	\N	f	f	f	1	\N	2023-09-15 11:21:47	JwnoJOFHOm	0	\N	2023-09-15 11:21:50	2023-09-15 11:21:50	\N
16457b1b-2c07-4d22-98e8-4817e9561867	Aisha Herzog	MALE	1963-01-01	yhomenick@example.com	dejah85	$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi	\N	\N	user	\N	\N	0	0	0	VN	vi	\N	0	\N	\N	\N	\N	\N	\N	f	f	f	1	\N	2023-09-15 11:21:47	EfP51nYGFJ	0	\N	2023-09-15 11:21:51	2023-09-15 11:21:51	\N
1a9b5721-132c-4801-9b58-59be2f49aed7	Ms. Virgie Littel	MALE	1973-01-01	brisa.oconner@example.net	goldner.jonathan	$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi	\N	\N	user	\N	\N	0	0	0	VN	vi	\N	0	\N	\N	\N	\N	\N	\N	f	f	f	1	\N	2023-09-15 11:21:47	mUV6bGuRus	0	\N	2023-09-15 11:21:51	2023-09-15 11:21:51	\N
48b37331-0e3e-48c0-a9ce-c388536d5e31	Juanita Rutherford	FEMALE	1981-01-01	treutel.jordy@example.com	moen.dovie	$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi	\N	\N	user	\N	\N	0	0	0	VN	vi	\N	0	\N	\N	\N	\N	\N	\N	f	f	f	1	\N	2023-09-15 11:21:47	Km63QoVMLI	0	\N	2023-09-15 11:21:51	2023-09-15 11:21:51	\N
ff0d9fab-77d4-4acd-95cb-d550271fdf4c	Eve Schultz	FEMALE	1960-01-01	ambrose.medhurst@example.net	pearline.schinner	$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi	\N	\N	user	\N	\N	0	0	0	VN	vi	\N	0	\N	\N	\N	\N	\N	\N	f	f	f	1	\N	2023-09-15 11:21:47	Ppl9A2hQWq	0	\N	2023-09-15 11:21:51	2023-09-15 11:21:51	\N
b873b2f8-117d-423b-9088-fd720e6772bc	Adella Cassin	MALE	1993-01-01	rosalinda.keeling@example.net	marcel.jaskolski	$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi	\N	\N	user	\N	\N	0	0	0	VN	vi	\N	0	\N	\N	\N	\N	\N	\N	f	f	f	1	\N	2023-09-15 11:21:47	uWxN0Rlo5t	0	\N	2023-09-15 11:21:51	2023-09-15 11:21:51	\N
d89197a0-701f-4a60-9a93-7806f525c5af	Brisa Russel III	FEMALE	1972-01-01	ethelyn.hackett@example.org	marquardt.connor	$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi	\N	\N	user	\N	\N	0	0	0	VN	vi	\N	0	\N	\N	\N	\N	\N	\N	f	f	f	1	\N	2023-09-15 11:21:47	ICoWq6IqLt	0	\N	2023-09-15 11:21:51	2023-09-15 11:21:51	\N
b79778e3-bae1-4ea8-8efd-c5083b712aff	Yesenia Rath	MALE	1967-01-01	olen.daniel@example.net	little.pasquale	$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi	\N	\N	user	\N	\N	0	0	0	VN	vi	\N	0	\N	\N	\N	\N	\N	\N	f	f	f	1	\N	2023-09-15 11:21:47	1bMTku9XZ2	0	\N	2023-09-15 11:21:51	2023-09-15 11:21:51	\N
1c5d70ff-8c18-4782-a6ab-513ec7fcf1f5	Jada Heidenreich	OTHER	1962-01-01	john54@example.net	cydney06	$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi	\N	\N	user	\N	\N	0	0	0	VN	vi	\N	0	\N	\N	\N	\N	\N	\N	f	f	f	1	\N	2023-09-15 11:21:47	NypOo535i6	0	\N	2023-09-15 11:21:51	2023-09-15 11:21:51	\N
45bca186-673c-4424-8f83-8d07551e2216	Eda Vandervort DDS	FEMALE	1970-01-01	dylan87@example.net	myra72	$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi	\N	\N	user	\N	\N	0	0	0	VN	vi	\N	0	\N	\N	\N	\N	\N	\N	f	f	f	1	\N	2023-09-15 11:21:47	tIYfPl6bnL	0	\N	2023-09-15 11:21:51	2023-09-15 11:21:51	\N
6844167f-06b0-46c1-b765-ab41f1107408	Miss Karlie Johnson	OTHER	1982-01-01	jacobi.unique@example.org	gretchen.kovacek	$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi	\N	\N	user	\N	\N	0	0	0	VN	vi	\N	0	\N	\N	\N	\N	\N	\N	f	f	f	1	\N	2023-09-15 11:21:47	3faZazX9Pb	0	\N	2023-09-15 11:21:51	2023-09-15 11:21:51	\N
31691c9f-0b9f-4d7c-a427-4976be23be07	Camron Nikolaus	FEMALE	1986-01-01	bschowalter@example.com	bode.gayle	$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi	\N	\N	user	\N	\N	0	0	0	VN	vi	\N	0	\N	\N	\N	\N	\N	\N	f	f	f	1	\N	2023-09-15 11:21:47	Y0oGSYKo5O	0	\N	2023-09-15 11:21:51	2023-09-15 11:21:51	\N
151480af-8228-4dae-becc-80a7bf594e36	Prof. Ismael Brekke II	OTHER	1980-01-01	alessandra.bosco@example.com	carter.ruthie	$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi	\N	\N	user	\N	\N	0	0	0	VN	vi	\N	0	\N	\N	\N	\N	\N	\N	f	f	f	1	\N	2023-09-15 11:21:47	mOEVm0u0wM	0	\N	2023-09-15 11:21:52	2023-09-15 11:21:52	\N
e91c05f0-b82e-45b3-9c2f-1624eee7cdc7	Tyler VonRueden	OTHER	1996-01-01	durgan.josue@example.com	judy22	$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi	\N	\N	user	\N	\N	0	0	0	VN	vi	\N	0	\N	\N	\N	\N	\N	\N	f	f	f	1	\N	2023-09-15 11:21:47	HgxHhzVuhv	0	\N	2023-09-15 11:21:52	2023-09-15 11:21:52	\N
883fc4e4-c0b8-4328-90c9-a68ae0cd0393	Pinkie Schoen	FEMALE	1980-01-01	block.ella@example.org	charlie92	$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi	\N	\N	user	\N	\N	0	0	0	VN	vi	\N	0	\N	\N	\N	\N	\N	\N	f	f	f	1	\N	2023-09-15 11:21:47	rErfoU6FwD	0	\N	2023-09-15 11:21:52	2023-09-15 11:21:52	\N
e1d4993b-a25e-47b0-862d-c0dded3c4eb4	Edwina Cartwright	MALE	1997-01-01	lindgren.kyla@example.net	qkub	$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi	\N	\N	user	\N	\N	0	0	0	VN	vi	\N	0	\N	\N	\N	\N	\N	\N	f	f	f	1	\N	2023-09-15 11:21:47	uTJgjmbLjx	0	\N	2023-09-15 11:21:52	2023-09-15 11:21:52	\N
64970d8d-40a8-4bba-b4d1-e1cb80319e29	Emilie Conroy	OTHER	1998-01-01	benny34@example.com	ross.lockman	$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi	\N	\N	user	\N	\N	0	0	0	VN	vi	\N	0	\N	\N	\N	\N	\N	\N	f	f	f	1	\N	2023-09-15 11:21:47	V416433MWi	0	\N	2023-09-15 11:21:52	2023-09-15 11:21:52	\N
cd1f128d-f206-44f8-a7b7-69921d9c002c	River Nolan	FEMALE	1983-01-01	marian12@example.net	stanford40	$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi	\N	\N	user	\N	\N	0	0	0	VN	vi	\N	0	\N	\N	\N	\N	\N	\N	f	f	f	1	\N	2023-09-15 11:21:47	28qn8pT4dd	0	\N	2023-09-15 11:21:52	2023-09-15 11:21:52	\N
aa071788-a8de-470a-ad39-e489a0cac9fc	Jamal Ritchie	MALE	1994-01-01	srice@example.org	alexandro.pfeffer	$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi	\N	\N	user	\N	\N	0	0	0	VN	vi	\N	0	\N	\N	\N	\N	\N	\N	f	f	f	1	\N	2023-09-15 11:21:47	TuDWPWUGXf	0	\N	2023-09-15 11:21:52	2023-09-15 11:21:52	\N
1effde64-a0b2-49c6-8258-92024ab08197	Kamryn Aufderhar	OTHER	1995-01-01	spfannerstill@example.org	welch.destin	$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi	\N	\N	user	\N	\N	0	0	0	VN	vi	\N	0	\N	\N	\N	\N	\N	\N	f	f	f	1	\N	2023-09-15 11:21:47	ropsFEp350	0	\N	2023-09-15 11:21:52	2023-09-15 11:21:52	\N
1c085745-e593-4bf8-9ac7-212c169d369e	Shanny O'Kon	MALE	1981-01-01	rowena15@example.net	kirlin.dixie	$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi	\N	\N	user	\N	\N	0	0	0	VN	vi	\N	0	\N	\N	\N	\N	\N	\N	f	f	f	1	\N	2023-09-15 11:21:47	YR86cZK478	0	\N	2023-09-15 11:21:52	2023-09-15 11:21:52	\N
f7e72591-5b34-41e7-aeca-17fa82db67c7	Kevin Schimmel	MALE	1997-01-01	assunta.willms@example.com	mschuppe	$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi	\N	\N	user	\N	\N	0	0	0	VN	vi	\N	0	\N	\N	\N	\N	\N	\N	f	f	f	1	\N	2023-09-15 11:21:47	yaEyzHlYRi	0	\N	2023-09-15 11:21:52	2023-09-15 11:21:52	\N
f1244012-31c9-432b-89ff-95ea599bf3f4	Ashleigh Pacocha	FEMALE	1982-01-01	von.madilyn@example.net	lfahey	$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi	\N	\N	user	\N	\N	0	0	0	VN	vi	\N	0	\N	\N	\N	\N	\N	\N	f	f	f	1	\N	2023-09-15 11:21:47	yYnI9UU80i	0	\N	2023-09-15 11:21:52	2023-09-15 11:21:52	\N
22ea4cc2-0dcb-4234-8807-f18d1dea7513	Joaquin Schroeder	MALE	1973-01-01	daphne19@example.org	schmidt.jermaine	$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi	\N	\N	user	\N	\N	0	0	0	VN	vi	\N	0	\N	\N	\N	\N	\N	\N	f	f	f	1	\N	2023-09-15 11:21:47	wLrJo5wSY0	0	\N	2023-09-15 11:21:53	2023-09-15 11:21:53	\N
cb633152-7a48-4f21-814d-40d86c98f882	Prof. Hellen Grimes I	OTHER	1993-01-01	ziemann.eugene@example.org	florine.ankunding	$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi	\N	\N	user	\N	\N	0	0	0	VN	vi	\N	0	\N	\N	\N	\N	\N	\N	f	f	f	1	\N	2023-09-15 11:21:47	LDLViZkXtF	0	\N	2023-09-15 11:21:53	2023-09-15 11:21:53	\N
d4302ca0-2873-410a-a9c0-ef8e61d15ada	Marcelle Strosin	MALE	1989-01-01	udoyle@example.org	ashly.beahan	$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi	\N	\N	user	\N	\N	0	0	0	VN	vi	\N	0	\N	\N	\N	\N	\N	\N	f	f	f	1	\N	2023-09-15 11:21:47	yHWsOYn1md	0	\N	2023-09-15 11:21:53	2023-09-15 11:21:53	\N
389d44aa-235c-4d11-bef6-2dde2d4e232c	Yasmine Marvin	MALE	1974-01-01	elisa.hill@example.org	graham.akeem	$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi	\N	\N	user	\N	\N	0	0	0	VN	vi	\N	0	\N	\N	\N	\N	\N	\N	f	f	f	1	\N	2023-09-15 11:21:47	dvJ9BrBL4A	0	\N	2023-09-15 11:21:53	2023-09-15 11:21:53	\N
4ba30126-85c3-4798-a5dd-1ce3730c86cc	Patience Feil	FEMALE	2003-01-01	qmuller@example.com	keeling.clair	$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi	\N	\N	user	\N	\N	0	0	0	VN	vi	\N	0	\N	\N	\N	\N	\N	\N	f	f	f	1	\N	2023-09-15 11:21:47	DsGbnvgZtN	0	\N	2023-09-15 11:21:53	2023-09-15 11:21:53	\N
23a1a503-78a1-4d7f-a628-b50aaf026f10	Adalberto Fahey DVM	OTHER	1961-01-01	xwalsh@example.com	albina43	$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi	\N	\N	user	\N	\N	0	0	0	VN	vi	\N	0	\N	\N	\N	\N	\N	\N	f	f	f	1	\N	2023-09-15 11:21:47	0vOdaqCMnf	0	\N	2023-09-15 11:21:53	2023-09-15 11:21:53	\N
3cbf2ce4-a06e-433f-a9e4-bd41ce3f985a	Miss Katrine Kshlerin	OTHER	1980-01-01	kritchie@example.org	dallin.kub	$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi	\N	\N	user	\N	\N	0	0	0	VN	vi	\N	0	\N	\N	\N	\N	\N	\N	f	f	f	1	\N	2023-09-15 11:21:47	FSmE6i7llw	0	\N	2023-09-15 11:21:53	2023-09-15 11:21:53	\N
ac7ac076-c9b3-4532-9ecc-fdba8c397083	Alena Parisian	FEMALE	1965-01-01	deion99@example.net	dax79	$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi	\N	\N	user	\N	\N	0	0	0	VN	vi	\N	0	\N	\N	\N	\N	\N	\N	f	f	f	1	\N	2023-09-15 11:21:47	hjgtkNLm6e	0	\N	2023-09-15 11:21:53	2023-09-15 11:21:53	\N
11df79cd-789b-4637-a6f7-d0c785d6a3b1	Justus O'Kon	MALE	1992-01-01	haylie.hackett@example.net	aschneider	$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi	\N	\N	user	\N	\N	0	0	0	VN	vi	\N	0	\N	\N	\N	\N	\N	\N	f	f	f	1	\N	2023-09-15 11:21:47	kZpXLsSTMr	0	\N	2023-09-15 11:21:53	2023-09-15 11:21:53	\N
c51ef61e-3b4a-4503-8bb1-a7ebdd0f06f1	Lesley Kemmer Sr.	OTHER	2006-01-01	reese24@example.org	yhagenes	$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi	\N	\N	user	\N	\N	0	0	0	VN	vi	\N	0	\N	\N	\N	\N	\N	\N	f	f	f	1	\N	2023-09-15 11:21:47	8KxuJYBW8K	0	\N	2023-09-15 11:21:53	2023-09-15 11:21:53	\N
48e54467-1547-445c-a01e-c53c4ffa6981	Alanis Larson II	MALE	1997-01-01	jgaylord@example.net	viola92	$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi	\N	\N	user	\N	\N	0	0	0	VN	vi	\N	0	\N	\N	\N	\N	\N	\N	f	f	f	1	\N	2023-09-15 11:21:47	5a4PxwWCN5	0	\N	2023-09-15 11:21:53	2023-09-15 11:21:53	\N
4b36239f-5e5d-4e36-a996-eab797e192c7	Jarrod Wilderman	OTHER	1978-01-01	delbert.dooley@example.com	margarette34	$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi	\N	\N	user	\N	\N	0	0	0	VN	vi	\N	0	\N	\N	\N	\N	\N	\N	f	f	f	1	\N	2023-09-15 11:21:47	LPRny8f6zf	0	\N	2023-09-15 11:21:54	2023-09-15 11:21:54	\N
364c161a-fde3-4398-bb07-be49ccd80e48	Katlynn Farrell	FEMALE	1963-01-01	payton28@example.net	vincent77	$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi	\N	\N	user	\N	\N	0	0	0	VN	vi	\N	0	\N	\N	\N	\N	\N	\N	f	f	f	1	\N	2023-09-15 11:21:47	Yww1qtbEu2	0	\N	2023-09-15 11:21:54	2023-09-15 11:21:54	\N
e75a391b-fb66-40d1-8f48-eb74fe8c7242	Omer Kuvalis	OTHER	1966-01-01	jacinthe27@example.net	reilly.raquel	$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi	\N	\N	user	\N	\N	0	0	0	VN	vi	\N	0	\N	\N	\N	\N	\N	\N	f	f	f	1	\N	2023-09-15 11:21:47	fyFXpYJXG0	0	\N	2023-09-15 11:21:54	2023-09-15 11:21:54	\N
bf60d8ab-4077-4241-8204-c958fc7b7347	Lexi Luettgen	OTHER	1963-01-01	istehr@example.org	xgislason	$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi	\N	\N	user	\N	\N	0	0	0	VN	vi	\N	0	\N	\N	\N	\N	\N	\N	f	f	f	1	\N	2023-09-15 11:21:47	0AFIE47l8H	0	\N	2023-09-15 11:21:54	2023-09-15 11:21:54	\N
7aad0dd2-1673-4234-9bdb-7fe24ef0db9c	Julianne Considine	MALE	1987-01-01	zblanda@example.org	golden.barton	$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi	\N	\N	user	\N	\N	0	0	0	VN	vi	\N	0	\N	\N	\N	\N	\N	\N	f	f	f	1	\N	2023-09-15 11:21:47	Jz0KTlmQjY	0	\N	2023-09-15 11:21:54	2023-09-15 11:21:54	\N
9166b3ae-9727-4ff5-8106-7c2594b15d68	Prof. Merl Walsh PhD	FEMALE	1972-01-01	elyssa46@example.org	gward	$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi	\N	\N	user	\N	\N	0	0	0	VN	vi	\N	0	\N	\N	\N	\N	\N	\N	f	f	f	1	\N	2023-09-15 11:21:47	vCKvNx93Qo	0	\N	2023-09-15 11:21:54	2023-09-15 11:21:54	\N
c9257a50-7614-49e5-9c72-1bf431bf4358	Vincenza Johnson	MALE	2006-01-01	fahey.don@example.org	kali22	$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi	\N	\N	user	\N	\N	0	0	0	VN	vi	\N	0	\N	\N	\N	\N	\N	\N	f	f	f	1	\N	2023-09-15 11:21:47	VbLIykiLKF	0	\N	2023-09-15 11:21:54	2023-09-15 11:21:54	\N
4e6ed913-b57b-4ca5-a0e7-0054af648c52	Fannie Conn	OTHER	1998-01-01	kreiger.bridget@example.net	oswaldo59	$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi	\N	\N	user	\N	\N	0	0	0	VN	vi	\N	0	\N	\N	\N	\N	\N	\N	f	f	f	1	\N	2023-09-15 11:21:47	erEECXH1WC	0	\N	2023-09-15 11:21:54	2023-09-15 11:21:54	\N
bd0f3065-89c0-40fd-9177-f72aba614e5d	Lennie Kuhic	FEMALE	2006-01-01	kenyatta76@example.net	mconnelly	$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi	\N	\N	user	\N	\N	0	0	0	VN	vi	\N	0	\N	\N	\N	\N	\N	\N	f	f	f	1	\N	2023-09-15 11:21:47	a4alYtI0Ih	0	\N	2023-09-15 11:21:54	2023-09-15 11:21:54	\N
dfcab684-c8b1-4e74-a082-9ff67dca641e	Mr. Verner Flatley MD	OTHER	1984-01-01	adaline.schmidt@example.net	howe.tavares	$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi	\N	\N	user	\N	\N	0	0	0	VN	vi	\N	0	\N	\N	\N	\N	\N	\N	f	f	f	1	\N	2023-09-15 11:21:47	53zc8Gb5HS	0	\N	2023-09-15 11:21:54	2023-09-15 11:21:54	\N
91b706df-6b8d-4c3f-b2d6-c4de23eb4e79	Dayana Donnelly	MALE	2000-01-01	hkoelpin@example.net	jake.metz	$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi	\N	\N	user	\N	\N	0	0	0	VN	vi	\N	0	\N	\N	\N	\N	\N	\N	f	f	f	1	\N	2023-09-15 11:21:47	xU0VzR8ma1	0	\N	2023-09-15 11:21:54	2023-09-15 11:21:54	\N
3ba74d25-eea7-4709-acca-5aacc759c41e	Laurianne Mraz V	MALE	1990-01-01	marquardt.ansley@example.net	hbosco	$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi	\N	\N	user	\N	\N	0	0	0	VN	vi	\N	0	\N	\N	\N	\N	\N	\N	f	f	f	1	\N	2023-09-15 11:21:47	hMGpA19Yuk	0	\N	2023-09-15 11:21:55	2023-09-15 11:21:55	\N
ee31b399-0d93-4944-84a3-162625f4c9c9	Ms. Cathrine Ferry	OTHER	1961-01-01	alta77@example.org	larry30	$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi	\N	\N	user	\N	\N	0	0	0	VN	vi	\N	0	\N	\N	\N	\N	\N	\N	f	f	f	1	\N	2023-09-15 11:21:47	O3TMJ1Yf34	0	\N	2023-09-15 11:21:55	2023-09-15 11:21:55	\N
7f3ef76c-e2f7-451d-a22a-d281999f8e93	Amparo Jaskolski I	MALE	1984-01-01	jschmidt@example.com	wilkinson.brandyn	$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi	\N	\N	user	\N	\N	0	0	0	VN	vi	\N	0	\N	\N	\N	\N	\N	\N	f	f	f	1	\N	2023-09-15 11:21:47	LijNBKdrLm	0	\N	2023-09-15 11:21:55	2023-09-15 11:21:55	\N
97bb053b-2aa0-45ae-9e81-9d4ab6b57846	Jamie Bradtke	FEMALE	1999-01-01	marks.conrad@example.com	johnny67	$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi	\N	\N	user	\N	\N	0	0	0	VN	vi	\N	0	\N	\N	\N	\N	\N	\N	f	f	f	1	\N	2023-09-15 11:21:47	J9Bg2oatNY	0	\N	2023-09-15 11:21:55	2023-09-15 11:21:55	\N
24929e11-d77e-4dae-b343-ce9141820964	Raleigh Veum	OTHER	1968-01-01	javonte.kessler@example.org	aboyle	$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi	\N	\N	user	\N	\N	0	0	0	VN	vi	\N	0	\N	\N	\N	\N	\N	\N	f	f	f	1	\N	2023-09-15 11:21:47	WwfEqbmnf4	0	\N	2023-09-15 11:21:55	2023-09-15 11:21:55	\N
a1910a01-12ec-4750-8d3b-d0842b273bec	Mr. Geo Brekke MD	OTHER	1973-01-01	bwest@example.org	qsauer	$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi	\N	\N	user	\N	\N	0	0	0	VN	vi	\N	0	\N	\N	\N	\N	\N	\N	f	f	f	1	\N	2023-09-15 11:21:47	ZXTNhmCWYP	0	\N	2023-09-15 11:21:55	2023-09-15 11:21:55	\N
7f1862c9-0f30-4edc-afac-c8609b228e25	Ewell Roob	MALE	1964-01-01	carolyne.mcglynn@example.org	alverta.doyle	$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi	\N	\N	user	\N	\N	0	0	0	VN	vi	\N	0	\N	\N	\N	\N	\N	\N	f	f	f	1	\N	2023-09-15 11:21:47	sUplcQ4Rt7	0	\N	2023-09-15 11:21:55	2023-09-15 11:21:55	\N
4cf1f3db-d9e3-4781-9d16-9610c13bcb25	Nikki Macejkovic	OTHER	2003-01-01	neoma95@example.org	mckenzie.deanna	$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi	\N	\N	user	\N	\N	0	0	0	VN	vi	\N	0	\N	\N	\N	\N	\N	\N	f	f	f	1	\N	2023-09-15 11:21:47	lNZsZGRixz	0	\N	2023-09-15 11:21:55	2023-09-15 11:21:55	\N
4eaa3f0c-c412-4ec6-b752-742e6605bd5e	Suzanne Huel	FEMALE	1980-01-01	dante67@example.org	bogan.eino	$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi	\N	\N	user	\N	\N	0	0	0	VN	vi	\N	0	\N	\N	\N	\N	\N	\N	f	f	f	1	\N	2023-09-15 11:21:47	RuP132V8SJ	0	\N	2023-09-15 11:21:56	2023-09-15 11:21:56	\N
47c08b53-659e-4be9-a1d0-9e22722923ad	Cleora Hirthe Jr.	MALE	1971-01-01	qbartoletti@example.net	bbecker	$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi	\N	\N	user	\N	\N	0	0	0	VN	vi	\N	0	\N	\N	\N	\N	\N	\N	f	f	f	1	\N	2023-09-15 11:21:47	Btu9d7PoIg	0	\N	2023-09-15 11:21:56	2023-09-15 11:21:56	\N
75191a78-8842-4c02-9fd6-cfabd5863373	Mateo Legros	MALE	2000-01-01	darryl.gorczany@example.org	doyle.billie	$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi	\N	\N	user	\N	\N	0	0	0	VN	vi	\N	0	\N	\N	\N	\N	\N	\N	f	f	f	1	\N	2023-09-15 11:21:47	UCEI7oOVpa	0	\N	2023-09-15 11:21:56	2023-09-15 11:21:56	\N
95009c8c-2a9f-4145-99a7-9e52a6fea30e	Prof. Mervin Cartwright	FEMALE	2005-01-01	angelina85@example.org	fbergnaum	$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi	\N	\N	user	\N	\N	0	0	0	VN	vi	\N	0	\N	\N	\N	\N	\N	\N	f	f	f	1	\N	2023-09-15 11:21:47	1rP8q4a90i	0	\N	2023-09-15 11:21:56	2023-09-15 11:21:56	\N
d3384129-894d-4c76-ac3c-00282f7b03c5	Allie Huels	OTHER	1967-01-01	mcglynn.everett@example.net	vlindgren	$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi	\N	\N	user	\N	\N	0	0	0	VN	vi	\N	0	\N	\N	\N	\N	\N	\N	f	f	f	1	\N	2023-09-15 11:21:47	i3kvNWRor1	0	\N	2023-09-15 11:21:56	2023-09-15 11:21:56	\N
05eba527-30c2-4521-b68c-cffbc1d26754	Prof. Kenyon Schuppe	MALE	1973-01-01	lesch.elliott@example.com	ygoyette	$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi	\N	\N	user	\N	\N	0	0	0	VN	vi	\N	0	\N	\N	\N	\N	\N	\N	f	f	f	1	\N	2023-09-15 11:21:47	K5u1jTTEMg	0	\N	2023-09-15 11:21:56	2023-09-15 11:21:56	\N
57a6e4de-2166-4a95-bb1c-c9063557f10b	Grover Cassin	OTHER	2001-01-01	nitzsche.marguerite@example.net	layne18	$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi	\N	\N	user	\N	\N	0	0	0	VN	vi	\N	0	\N	\N	\N	\N	\N	\N	f	f	f	1	\N	2023-09-15 11:21:47	8xL2QJHXvF	0	\N	2023-09-15 11:21:56	2023-09-15 11:21:56	\N
a0e32e91-88ce-4929-98f7-1fb27bc8b44b	Sydnee Gislason	OTHER	1969-01-01	dmurazik@example.com	zbailey	$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi	\N	\N	user	\N	\N	0	0	0	VN	vi	\N	0	\N	\N	\N	\N	\N	\N	f	f	f	1	\N	2023-09-15 11:21:47	jAQe5WHgmx	0	\N	2023-09-15 11:21:56	2023-09-15 11:21:56	\N
\.


--
-- Data for Name: vouchers; Type: TABLE DATA; Schema: public; Owner: trekka
--

COPY public.vouchers (uuid, campaign_uuid, user_uuid, claim_user_uuid, type, code, ref_url, payment_url, status, is_claim, claim_expired_at, expired_at, created_at, updated_at) FROM stdin;
ee0f4c85-0992-4edd-ba6d-a62ca4a56a85	fc469cbb-4513-46aa-95f1-dd91a2afd199	7aad0dd2-1673-4234-9bdb-7fe24ef0db9c	\N	tour	EB0542	\N	\N	idle	0	2023-09-18 11:34:03	2023-09-23 11:19:03	2023-09-18 11:19:03	2023-09-18 11:19:03
ca88c50e-4b37-4094-9f2d-a767eed4f261	fc469cbb-4513-46aa-95f1-dd91a2afd199	e91c05f0-b82e-45b3-9c2f-1624eee7cdc7	\N	tour	487440	\N	\N	idle	0	2023-09-18 11:34:03	2023-09-23 11:19:03	2023-09-18 11:19:03	2023-09-18 11:19:03
116d99db-79de-4d1c-8573-78a1f48d23e8	fc469cbb-4513-46aa-95f1-dd91a2afd199	4c2222bb-6c2d-4307-9e10-cc09254bccdd	\N	tour	E94C36	\N	\N	idle	0	2023-09-18 11:34:03	2023-09-23 11:19:03	2023-09-18 11:19:03	2023-09-18 11:19:03
a31f39c7-dd39-450a-9fef-96ede5617b92	fc469cbb-4513-46aa-95f1-dd91a2afd199	05eba527-30c2-4521-b68c-cffbc1d26754	\N	tour	B943F4	\N	\N	idle	0	2023-09-18 11:34:03	2023-09-23 11:19:03	2023-09-18 11:19:03	2023-09-18 11:19:03
08ba0b40-da54-4955-9df9-08e3f3aa5c37	fc469cbb-4513-46aa-95f1-dd91a2afd199	4e6ed913-b57b-4ca5-a0e7-0054af648c52	\N	tour	04E925	\N	\N	idle	0	2023-09-18 11:34:03	2023-09-23 11:19:03	2023-09-18 11:19:03	2023-09-18 11:19:03
d767ff7e-4376-4231-a088-72cefa666515	fc469cbb-4513-46aa-95f1-dd91a2afd199	75191a78-8842-4c02-9fd6-cfabd5863373	\N	tour	755BAB	\N	\N	idle	0	2023-09-18 11:34:03	2023-09-23 11:19:03	2023-09-18 11:19:03	2023-09-18 11:19:03
c96f81e2-3619-4882-9519-ab7d6e96a15e	fc469cbb-4513-46aa-95f1-dd91a2afd199	a1910a01-12ec-4750-8d3b-d0842b273bec	\N	tour	8A84E0	\N	\N	idle	0	2023-09-18 11:34:03	2023-09-23 11:19:03	2023-09-18 11:19:03	2023-09-18 11:19:03
7d78be0c-728f-4779-b0b2-9fc43036ebf8	fc469cbb-4513-46aa-95f1-dd91a2afd199	717dc3c0-0432-4cdc-8563-2898b6e2f1b2	\N	tour	A7D2F7	\N	\N	idle	0	2023-09-18 11:34:03	2023-09-23 11:19:03	2023-09-18 11:19:03	2023-09-18 11:19:03
fcce3146-eab2-4d29-9423-ee14c2236923	fc469cbb-4513-46aa-95f1-dd91a2afd199	ed0250b7-6b9c-4537-85fa-4f0526efc740	\N	tour	713DF5	\N	\N	idle	0	2023-09-18 11:34:03	2023-09-23 11:19:03	2023-09-18 11:19:03	2023-09-18 11:19:03
d26e2582-ed78-4f84-86b9-8e956afbe012	fc469cbb-4513-46aa-95f1-dd91a2afd199	883fc4e4-c0b8-4328-90c9-a68ae0cd0393	\N	tour	C90B15	\N	\N	idle	0	2023-09-18 11:34:03	2023-09-23 11:19:03	2023-09-18 11:19:03	2023-09-18 11:19:03
6c7e3831-4cb1-43d3-a4e9-eb6832ed3942	fc469cbb-4513-46aa-95f1-dd91a2afd199	d6826726-4b86-4f1e-b656-4e84b84f077b	\N	tour	DCF0C9	\N	\N	idle	0	2023-09-18 11:34:03	2023-09-23 11:19:03	2023-09-18 11:19:03	2023-09-18 11:19:03
2ec2fbd2-5825-4d44-aebf-edb8d3f9e9d6	fc469cbb-4513-46aa-95f1-dd91a2afd199	aa071788-a8de-470a-ad39-e489a0cac9fc	\N	tour	A695CC	\N	\N	idle	0	2023-09-18 11:34:03	2023-09-23 11:19:03	2023-09-18 11:19:03	2023-09-18 11:19:03
85366902-e323-473d-aa2f-d45b22dcdc07	fc469cbb-4513-46aa-95f1-dd91a2afd199	1a9b5721-132c-4801-9b58-59be2f49aed7	\N	tour	79DE89	\N	\N	idle	0	2023-09-18 11:34:03	2023-09-23 11:19:03	2023-09-18 11:19:03	2023-09-18 11:19:03
4524e029-dbff-4818-a504-bc9b29ea5d39	fc469cbb-4513-46aa-95f1-dd91a2afd199	91b706df-6b8d-4c3f-b2d6-c4de23eb4e79	\N	tour	F3FDAF	\N	\N	idle	0	2023-09-18 11:34:03	2023-09-23 11:19:03	2023-09-18 11:19:03	2023-09-18 11:19:03
fa0a1c15-318e-4f34-b752-9aa970e474c2	fc469cbb-4513-46aa-95f1-dd91a2afd199	545bc196-1795-48fb-9c2c-3e55b3e6a2a2	\N	tour	A396B3	\N	\N	idle	0	2023-09-18 11:34:03	2023-09-23 11:19:03	2023-09-18 11:19:03	2023-09-18 11:19:03
0da31fe1-1918-4169-a0e6-fb4ea42a1538	fc469cbb-4513-46aa-95f1-dd91a2afd199	4b36239f-5e5d-4e36-a996-eab797e192c7	\N	tour	82BC33	\N	\N	idle	0	2023-09-18 11:34:03	2023-09-23 11:19:03	2023-09-18 11:19:03	2023-09-18 11:19:03
92eff1bd-18da-4c8f-9d3b-ee7ce125a638	fc469cbb-4513-46aa-95f1-dd91a2afd199	696ed4ee-f6df-4e67-b067-741394ef70d4	\N	tour	086D65	\N	\N	idle	0	2023-09-18 11:34:03	2023-09-23 11:19:03	2023-09-18 11:19:03	2023-09-18 11:19:03
ddec2731-25f0-40b4-bc57-c99fbfe8f52f	fc469cbb-4513-46aa-95f1-dd91a2afd199	48b37331-0e3e-48c0-a9ce-c388536d5e31	\N	tour	F90147	\N	\N	idle	0	2023-09-18 11:34:03	2023-09-23 11:19:03	2023-09-18 11:19:03	2023-09-18 11:19:03
895d824a-0a15-4fc8-b3df-afc412887f66	fc469cbb-4513-46aa-95f1-dd91a2afd199	d89197a0-701f-4a60-9a93-7806f525c5af	\N	tour	71E002	\N	\N	idle	0	2023-09-18 11:34:03	2023-09-23 11:19:03	2023-09-18 11:19:03	2023-09-18 11:19:03
7f047476-2e72-4733-a87f-8e06b73759fc	6c337fb5-1b8d-4fa5-bebd-2c00c0f4ecfb	a247238f-aa4c-497c-9faa-26320d87580f	\N	tour	CAA627	\N	\N	idle	0	2023-09-18 11:21:03	2023-09-23 11:20:03	2023-09-18 11:20:03	2023-09-18 11:20:03
6b155366-93d8-4bf6-9cc8-19232abe3a4a	6c337fb5-1b8d-4fa5-bebd-2c00c0f4ecfb	3ba74d25-eea7-4709-acca-5aacc759c41e	\N	tour	36599A	\N	\N	idle	0	2023-09-18 11:21:03	2023-09-23 11:20:03	2023-09-18 11:20:03	2023-09-18 11:20:03
55d904dc-957c-4c78-a026-e98f5404c175	6c337fb5-1b8d-4fa5-bebd-2c00c0f4ecfb	7f3ef76c-e2f7-451d-a22a-d281999f8e93	\N	tour	19FCD9	\N	\N	idle	0	2023-09-18 11:21:03	2023-09-23 11:20:03	2023-09-18 11:20:03	2023-09-18 11:20:03
fa0d3cb7-161c-4e09-8690-47a020885ed7	6c337fb5-1b8d-4fa5-bebd-2c00c0f4ecfb	7c6af492-c1c1-4e4f-8b1e-e63c98b0734a	\N	tour	717E29	\N	\N	idle	0	2023-09-18 11:21:03	2023-09-23 11:20:03	2023-09-18 11:20:03	2023-09-18 11:20:03
8054d7cc-5c8e-49d2-9bf9-7b8746ec57ca	6c337fb5-1b8d-4fa5-bebd-2c00c0f4ecfb	717dc3c0-0432-4cdc-8563-2898b6e2f1b2	\N	tour	75E0F0	\N	\N	idle	0	2023-09-18 11:21:03	2023-09-23 11:20:03	2023-09-18 11:20:03	2023-09-18 11:20:03
f2a1e446-a53b-424c-9812-535252fe6201	6c337fb5-1b8d-4fa5-bebd-2c00c0f4ecfb	64970d8d-40a8-4bba-b4d1-e1cb80319e29	\N	tour	66E216	\N	\N	idle	0	2023-09-18 11:21:03	2023-09-23 11:20:03	2023-09-18 11:20:03	2023-09-18 11:20:03
73ec23bd-8b65-4bb9-9439-38b0a222958e	6c337fb5-1b8d-4fa5-bebd-2c00c0f4ecfb	61028bae-8473-407f-a7ae-25a163387e38	\N	tour	4CDE46	\N	\N	idle	0	2023-09-18 11:21:03	2023-09-23 11:20:03	2023-09-18 11:20:03	2023-09-18 11:20:03
b7e6cb92-da73-44a9-8707-d2d67fe7adc7	6c337fb5-1b8d-4fa5-bebd-2c00c0f4ecfb	1a9b5721-132c-4801-9b58-59be2f49aed7	\N	tour	1D8DC4	\N	\N	idle	0	2023-09-18 11:21:03	2023-09-23 11:20:03	2023-09-18 11:20:03	2023-09-18 11:20:03
bcb8f9ae-2277-4a72-a699-cb304ea3b4b5	6c337fb5-1b8d-4fa5-bebd-2c00c0f4ecfb	545bc196-1795-48fb-9c2c-3e55b3e6a2a2	\N	tour	D701DD	\N	\N	idle	0	2023-09-18 11:21:03	2023-09-23 11:20:03	2023-09-18 11:20:03	2023-09-18 11:20:03
c890a112-49a0-44ea-9b03-fcc090cf85d2	6c337fb5-1b8d-4fa5-bebd-2c00c0f4ecfb	cb633152-7a48-4f21-814d-40d86c98f882	\N	tour	BE76C6	\N	\N	idle	0	2023-09-18 11:21:03	2023-09-23 11:20:03	2023-09-18 11:20:03	2023-09-18 11:20:03
369aa5d9-e839-483b-b82d-a6faa29828c4	6c337fb5-1b8d-4fa5-bebd-2c00c0f4ecfb	883fc4e4-c0b8-4328-90c9-a68ae0cd0393	\N	tour	8BC949	\N	\N	idle	0	2023-09-18 11:21:03	2023-09-23 11:20:03	2023-09-18 11:20:03	2023-09-18 11:20:03
39fdafef-fc34-4fd9-8441-9fb93edafe5a	6c337fb5-1b8d-4fa5-bebd-2c00c0f4ecfb	48e54467-1547-445c-a01e-c53c4ffa6981	\N	tour	154606	\N	\N	idle	0	2023-09-18 11:21:03	2023-09-23 11:20:03	2023-09-18 11:20:03	2023-09-18 11:20:03
d94a53b8-169d-4bdd-9c6a-f20f81811904	6c337fb5-1b8d-4fa5-bebd-2c00c0f4ecfb	6844167f-06b0-46c1-b765-ab41f1107408	\N	tour	49A64F	\N	\N	idle	0	2023-09-18 11:21:03	2023-09-23 11:20:03	2023-09-18 11:20:03	2023-09-18 11:20:03
fe152390-4a95-4e14-bf4c-dede1538e618	6c337fb5-1b8d-4fa5-bebd-2c00c0f4ecfb	4b36239f-5e5d-4e36-a996-eab797e192c7	\N	tour	8A33FC	\N	\N	idle	0	2023-09-18 11:21:03	2023-09-23 11:20:03	2023-09-18 11:20:03	2023-09-18 11:20:03
430383f6-192d-47a9-8a2c-d7f3369b719a	6c337fb5-1b8d-4fa5-bebd-2c00c0f4ecfb	802e09cb-d7de-4621-95e4-69a56436ad15	\N	tour	A1833D	\N	\N	idle	0	2023-09-18 11:21:03	2023-09-23 11:20:03	2023-09-18 11:20:03	2023-09-18 11:20:03
be1dafa2-ed3f-4fac-8c7b-3cda303dc77b	6c337fb5-1b8d-4fa5-bebd-2c00c0f4ecfb	e1d4993b-a25e-47b0-862d-c0dded3c4eb4	\N	tour	90ED56	\N	\N	idle	0	2023-09-18 11:21:03	2023-09-23 11:20:03	2023-09-18 11:20:03	2023-09-18 11:20:03
fb56c1d5-3293-431e-8cc9-cb6e76c214fc	6c337fb5-1b8d-4fa5-bebd-2c00c0f4ecfb	a1910a01-12ec-4750-8d3b-d0842b273bec	\N	tour	EA7166	\N	\N	idle	0	2023-09-18 11:21:03	2023-09-23 11:20:03	2023-09-18 11:20:03	2023-09-18 11:20:03
205bb194-5b66-4196-81ba-a105a26c513c	6c337fb5-1b8d-4fa5-bebd-2c00c0f4ecfb	4b1cec5e-f11e-48d6-9238-22e32f76ff8d	\N	tour	5648DD	\N	\N	idle	0	2023-09-18 11:21:03	2023-09-23 11:20:03	2023-09-18 11:20:03	2023-09-18 11:20:03
6d5944bf-ae31-4a88-8d6c-1923b19c3d16	6c337fb5-1b8d-4fa5-bebd-2c00c0f4ecfb	22ea4cc2-0dcb-4234-8807-f18d1dea7513	\N	tour	499342	\N	\N	idle	0	2023-09-18 11:21:03	2023-09-23 11:20:03	2023-09-18 11:20:03	2023-09-18 11:20:03
1bf39c1a-53b9-4d6f-92ef-bda6f10cbd84	6c337fb5-1b8d-4fa5-bebd-2c00c0f4ecfb	b873b2f8-117d-423b-9088-fd720e6772bc	\N	tour	BF0E38	\N	\N	idle	0	2023-09-18 11:21:03	2023-09-23 11:20:03	2023-09-18 11:20:03	2023-09-18 11:20:03
fb2b8491-8eb5-4b72-bf97-4c40ee4312ba	fc469cbb-4513-46aa-95f1-dd91a2afd199	389d44aa-235c-4d11-bef6-2dde2d4e232c	\N	tour	52BD0F	\N	\N	idle	0	2023-09-18 11:34:03	2023-09-23 11:19:03	2023-09-18 11:19:03	2023-09-18 11:19:03
d0f0725b-ca48-479b-ac92-b169ee95f248	fc469cbb-4513-46aa-95f1-dd91a2afd199	f1244012-31c9-432b-89ff-95ea599bf3f4	\N	tour	2F6149	\N	\N	idle	0	2023-09-18 11:34:03	2023-09-23 11:19:03	2023-09-18 11:19:03	2023-09-18 11:19:03
88bbfd2e-5fa1-4b27-b174-ba9487d571bb	fc469cbb-4513-46aa-95f1-dd91a2afd199	b873b2f8-117d-423b-9088-fd720e6772bc	\N	tour	79DD18	\N	\N	idle	0	2023-09-18 11:34:03	2023-09-23 11:19:03	2023-09-18 11:19:03	2023-09-18 11:19:03
d9f845d2-ba37-4d7c-824d-4436a8545fb0	fc469cbb-4513-46aa-95f1-dd91a2afd199	a0e32e91-88ce-4929-98f7-1fb27bc8b44b	\N	tour	510ADA	\N	\N	idle	0	2023-09-18 11:34:03	2023-09-23 11:19:03	2023-09-18 11:19:03	2023-09-18 11:19:03
1ee77c88-039d-4284-915c-2edd5e641a42	fc469cbb-4513-46aa-95f1-dd91a2afd199	16d80903-7d01-45c1-8a33-01496ca22b63	\N	tour	63B41B	\N	\N	idle	0	2023-09-18 11:34:03	2023-09-23 11:19:03	2023-09-18 11:19:03	2023-09-18 11:19:03
5609ec66-4d67-49e4-a2e5-2d44f4dee31c	fc469cbb-4513-46aa-95f1-dd91a2afd199	6844167f-06b0-46c1-b765-ab41f1107408	\N	tour	75DD1C	\N	\N	idle	0	2023-09-18 11:34:03	2023-09-23 11:19:03	2023-09-18 11:19:03	2023-09-18 11:19:03
a7b759ea-0326-44a3-a904-c3b9a2c36ca7	fc469cbb-4513-46aa-95f1-dd91a2afd199	4eaa3f0c-c412-4ec6-b752-742e6605bd5e	\N	tour	E6F59B	\N	\N	idle	0	2023-09-18 11:34:03	2023-09-23 11:19:03	2023-09-18 11:19:03	2023-09-18 11:19:03
125917e1-ff46-41eb-9daa-84ab9fff104b	fc469cbb-4513-46aa-95f1-dd91a2afd199	3369dc9d-eced-4c73-b055-c2a10299413b	\N	tour	914F52	\N	\N	idle	0	2023-09-18 11:34:03	2023-09-23 11:19:03	2023-09-18 11:19:03	2023-09-18 11:19:03
0f488e3b-07ad-4903-a22c-dd255078fccb	fc469cbb-4513-46aa-95f1-dd91a2afd199	2a6aef61-abc3-44a7-98d1-e5c1c47058a3	\N	tour	254FBA	\N	\N	idle	0	2023-09-18 11:34:03	2023-09-23 11:19:03	2023-09-18 11:19:03	2023-09-18 11:19:03
3ce51e01-7d29-48b7-9fd2-fce7ecb58dee	fc469cbb-4513-46aa-95f1-dd91a2afd199	af19001d-a302-4733-b08e-93b95686e307	\N	tour	7194D3	\N	\N	idle	0	2023-09-18 11:34:03	2023-09-23 11:19:03	2023-09-18 11:19:03	2023-09-18 11:19:03
e33ce5f6-3b36-4b11-acf8-9c918d0a5fcd	fc469cbb-4513-46aa-95f1-dd91a2afd199	5ab97801-7ce3-477d-92f9-720b20fbb958	5ab97801-7ce3-477d-92f9-720b20fbb958	tour	FAB80F	\N	\N	claimed	1	2023-09-16 18:17:03	2023-09-21 18:02:03	2023-09-16 18:02:03	2023-09-16 18:02:03
14f50425-f0b1-4bbd-a6cc-cb6fa4fa7bdb	fc469cbb-4513-46aa-95f1-dd91a2afd199	1effde64-a0b2-49c6-8258-92024ab08197	\N	tour	E9B5ED	\N	\N	idle	0	2023-09-18 11:34:03	2023-09-23 11:19:03	2023-09-18 11:19:03	2023-09-18 11:19:03
598ed016-49cb-4dc1-a0bb-3f201ec49fd6	fc469cbb-4513-46aa-95f1-dd91a2afd199	002bb462-75c7-42eb-8677-87ea8e921c62	\N	tour	F1D609	\N	\N	idle	0	2023-09-18 11:34:03	2023-09-23 11:19:03	2023-09-18 11:19:03	2023-09-18 11:19:03
005367c6-6283-4a72-80bc-8a1b72192e99	fc469cbb-4513-46aa-95f1-dd91a2afd199	3ba74d25-eea7-4709-acca-5aacc759c41e	\N	tour	C40C00	\N	\N	idle	0	2023-09-18 11:34:03	2023-09-23 11:19:03	2023-09-18 11:19:03	2023-09-18 11:19:03
a6790910-e086-41f6-8415-8330fae3c2cd	fc469cbb-4513-46aa-95f1-dd91a2afd199	7c6af492-c1c1-4e4f-8b1e-e63c98b0734a	\N	tour	7B11F0	\N	\N	idle	0	2023-09-18 11:34:03	2023-09-23 11:19:03	2023-09-18 11:19:03	2023-09-18 11:19:03
3958497e-4bb7-4c41-92c6-84bebaf72dfd	fc469cbb-4513-46aa-95f1-dd91a2afd199	5c5b1d29-b2cc-4b92-bd5f-36d0521c77ac	\N	tour	2EC1DF	\N	\N	idle	0	2023-09-18 11:34:03	2023-09-23 11:19:03	2023-09-18 11:19:03	2023-09-18 11:19:03
17301387-5eb7-4099-aa1e-c86c584355db	fc469cbb-4513-46aa-95f1-dd91a2afd199	b0d453da-f96a-4798-9e4e-98da4b01b7d5	\N	tour	0FFC9C	\N	\N	idle	0	2023-09-18 11:34:03	2023-09-23 11:19:03	2023-09-18 11:19:03	2023-09-18 11:19:03
b88bb64e-5ecc-4a24-aada-55d1fe348287	fc469cbb-4513-46aa-95f1-dd91a2afd199	0a9a7c61-702d-4e90-8e04-26c5dd2f3624	\N	tour	4DCBD7	\N	\N	idle	0	2023-09-18 11:34:03	2023-09-23 11:19:03	2023-09-18 11:19:03	2023-09-18 11:19:03
f279568a-f2da-47a4-bc9d-d41979636ca6	fc469cbb-4513-46aa-95f1-dd91a2afd199	4d01eea9-0fd0-4868-a28e-228cfa890291	\N	tour	EBA0EC	\N	\N	idle	0	2023-09-18 11:34:03	2023-09-23 11:19:03	2023-09-18 11:19:03	2023-09-18 11:19:03
fa020c94-0622-4f0d-9365-fb106b9fbe7c	fc469cbb-4513-46aa-95f1-dd91a2afd199	1c085745-e593-4bf8-9ac7-212c169d369e	\N	tour	F2023E	\N	\N	idle	0	2023-09-18 11:34:03	2023-09-23 11:19:03	2023-09-18 11:19:03	2023-09-18 11:19:03
31300c4e-678b-45f5-a80b-024ac098a6ad	fc469cbb-4513-46aa-95f1-dd91a2afd199	47c08b53-659e-4be9-a1d0-9e22722923ad	\N	tour	B6FBF0	\N	\N	idle	0	2023-09-18 11:34:03	2023-09-23 11:19:03	2023-09-18 11:19:03	2023-09-18 11:19:03
8cabeb15-9ddf-40cb-a958-1fb619330312	fc469cbb-4513-46aa-95f1-dd91a2afd199	389b9311-80ff-4e4b-8b9f-8d34612bed78	\N	tour	30EB58	\N	\N	idle	0	2023-09-18 11:34:03	2023-09-23 11:19:03	2023-09-18 11:19:03	2023-09-18 11:19:03
d2e94489-ccf3-48fc-a44e-efe75beb0850	fc469cbb-4513-46aa-95f1-dd91a2afd199	d4302ca0-2873-410a-a9c0-ef8e61d15ada	\N	tour	77D562	\N	\N	idle	0	2023-09-18 11:34:03	2023-09-23 11:19:03	2023-09-18 11:19:03	2023-09-18 11:19:03
d6093876-9dd6-4122-a3b7-a3747c0bebd6	fc469cbb-4513-46aa-95f1-dd91a2afd199	7f6ab7df-e87d-4989-9025-68625bb3dde6	\N	tour	12C6F9	\N	\N	idle	0	2023-09-18 11:34:03	2023-09-23 11:19:03	2023-09-18 11:19:03	2023-09-18 11:19:03
be9d1d46-87d2-45df-92e4-96d8a55766ca	fc469cbb-4513-46aa-95f1-dd91a2afd199	dfcab684-c8b1-4e74-a082-9ff67dca641e	\N	tour	AE8F42	\N	\N	idle	0	2023-09-18 11:34:03	2023-09-23 11:19:03	2023-09-18 11:19:03	2023-09-18 11:19:03
db246743-ea8e-4a92-91ff-629c79b80a48	fc469cbb-4513-46aa-95f1-dd91a2afd199	802e09cb-d7de-4621-95e4-69a56436ad15	\N	tour	C30ABD	\N	\N	idle	0	2023-09-18 11:34:03	2023-09-23 11:19:03	2023-09-18 11:19:03	2023-09-18 11:19:03
441259f7-619b-4308-a48a-188783e19c85	fc469cbb-4513-46aa-95f1-dd91a2afd199	151480af-8228-4dae-becc-80a7bf594e36	\N	tour	AB4B0D	\N	\N	idle	0	2023-09-18 11:34:03	2023-09-23 11:19:03	2023-09-18 11:19:03	2023-09-18 11:19:03
00f88d98-ccfd-48ef-afbb-c8716fcd62c4	fc469cbb-4513-46aa-95f1-dd91a2afd199	e1d4993b-a25e-47b0-862d-c0dded3c4eb4	\N	tour	C1F033	\N	\N	idle	0	2023-09-18 11:34:03	2023-09-23 11:19:03	2023-09-18 11:19:03	2023-09-18 11:19:03
668675c3-f004-4b8e-be4c-2d5578febad9	fc469cbb-4513-46aa-95f1-dd91a2afd199	9166b3ae-9727-4ff5-8106-7c2594b15d68	\N	tour	25C412	\N	\N	idle	0	2023-09-18 11:34:03	2023-09-23 11:19:03	2023-09-18 11:19:03	2023-09-18 11:19:03
06dec470-9992-49c8-a270-2d2e4816a5ae	fc469cbb-4513-46aa-95f1-dd91a2afd199	48e54467-1547-445c-a01e-c53c4ffa6981	\N	tour	C8F546	\N	\N	idle	0	2023-09-18 11:34:03	2023-09-23 11:19:03	2023-09-18 11:19:03	2023-09-18 11:19:03
077ed4c2-64d3-4f27-9c9f-5d530812cd5d	fc469cbb-4513-46aa-95f1-dd91a2afd199	3371fe17-56e2-4587-8dda-e728d52b6bd6	\N	tour	2536CF	\N	\N	idle	0	2023-09-18 11:34:03	2023-09-23 11:19:03	2023-09-18 11:19:03	2023-09-18 11:19:03
9207e18a-6b4d-4951-82e2-bdce057b673b	fc469cbb-4513-46aa-95f1-dd91a2afd199	cd1f128d-f206-44f8-a7b7-69921d9c002c	\N	tour	8A184A	\N	\N	idle	0	2023-09-18 11:34:03	2023-09-23 11:19:03	2023-09-18 11:19:03	2023-09-18 11:19:03
3cd54b90-be50-4c5d-b51f-99fd3f2efcb8	fc469cbb-4513-46aa-95f1-dd91a2afd199	e1ec17f5-5826-4be2-8ef0-d66f52fc9799	\N	tour	F0575A	\N	\N	idle	0	2023-09-18 11:34:03	2023-09-23 11:19:03	2023-09-18 11:19:03	2023-09-18 11:19:03
330a59f0-8dd7-4734-af7a-d553e859f681	fc469cbb-4513-46aa-95f1-dd91a2afd199	ddf74034-d166-4563-9301-4f8ccd84e28b	\N	tour	F6BCC2	\N	\N	idle	0	2023-09-18 11:34:03	2023-09-23 11:19:03	2023-09-18 11:19:03	2023-09-18 11:19:03
2218f517-4186-4195-b19c-4c7a488d2c24	fc469cbb-4513-46aa-95f1-dd91a2afd199	dfe1397a-fd81-4bec-8dc6-fc11f0fe2671	\N	tour	ABCBB3	\N	\N	idle	0	2023-09-18 11:34:03	2023-09-23 11:19:03	2023-09-18 11:19:03	2023-09-18 11:19:03
f6815f23-d159-4aa8-a595-dd051b2418c6	fc469cbb-4513-46aa-95f1-dd91a2afd199	24929e11-d77e-4dae-b343-ce9141820964	\N	tour	FE7C33	\N	\N	idle	0	2023-09-18 11:34:03	2023-09-23 11:19:03	2023-09-18 11:19:03	2023-09-18 11:19:03
f34cd631-b348-4316-845d-4b51dd981f1b	fc469cbb-4513-46aa-95f1-dd91a2afd199	3cbf2ce4-a06e-433f-a9e4-bd41ce3f985a	\N	tour	26DEB3	\N	\N	idle	0	2023-09-18 11:34:03	2023-09-23 11:19:03	2023-09-18 11:19:03	2023-09-18 11:19:03
cd5d659e-e3c1-4abf-ad81-52eb247bec8a	fc469cbb-4513-46aa-95f1-dd91a2afd199	553f6965-2f1f-45a8-bc47-6fc099b64e87	\N	tour	B4F5E1	\N	\N	idle	0	2023-09-18 11:34:03	2023-09-23 11:19:03	2023-09-18 11:19:03	2023-09-18 11:19:03
94d66812-4afa-4f3c-aa5f-ca84f6ab63df	fc469cbb-4513-46aa-95f1-dd91a2afd199	31691c9f-0b9f-4d7c-a427-4976be23be07	\N	tour	1134B0	\N	\N	idle	0	2023-09-18 11:34:03	2023-09-23 11:19:03	2023-09-18 11:19:03	2023-09-18 11:19:03
9278b394-9926-4e8f-9f08-89482bc2afcb	fc469cbb-4513-46aa-95f1-dd91a2afd199	45bca186-673c-4424-8f83-8d07551e2216	\N	tour	B53699	\N	\N	idle	0	2023-09-18 11:34:03	2023-09-23 11:19:03	2023-09-18 11:19:03	2023-09-18 11:19:03
3cbfcec6-c626-448e-89d3-c7ace70b9bfd	fc469cbb-4513-46aa-95f1-dd91a2afd199	d08a8648-cfff-492a-84cf-d95eeea78ad7	\N	tour	166060	\N	\N	idle	0	2023-09-18 11:34:03	2023-09-23 11:19:03	2023-09-18 11:19:03	2023-09-18 11:19:03
d4bc40e6-b98e-4665-9500-72911e94c1aa	fc469cbb-4513-46aa-95f1-dd91a2afd199	a247238f-aa4c-497c-9faa-26320d87580f	\N	tour	9C9AD5	\N	\N	idle	0	2023-09-18 11:34:03	2023-09-23 11:19:03	2023-09-18 11:19:03	2023-09-18 11:19:03
f0f8f8c7-7436-478a-98e8-1622fa683036	fc469cbb-4513-46aa-95f1-dd91a2afd199	61028bae-8473-407f-a7ae-25a163387e38	\N	tour	1761A0	\N	\N	idle	0	2023-09-18 11:34:03	2023-09-23 11:19:03	2023-09-18 11:19:03	2023-09-18 11:19:03
5dbf927c-24eb-49d5-a693-7fade5f45321	fc469cbb-4513-46aa-95f1-dd91a2afd199	64970d8d-40a8-4bba-b4d1-e1cb80319e29	\N	tour	B763D7	\N	\N	idle	0	2023-09-18 11:34:03	2023-09-23 11:19:03	2023-09-18 11:19:03	2023-09-18 11:19:03
8ba88069-b82d-4cc2-a13d-6a9b01cee0ae	fc469cbb-4513-46aa-95f1-dd91a2afd199	97bb053b-2aa0-45ae-9e81-9d4ab6b57846	\N	tour	0F2653	\N	\N	idle	0	2023-09-18 11:34:03	2023-09-23 11:19:03	2023-09-18 11:19:03	2023-09-18 11:19:03
07df437f-8fa9-4252-946d-ccf26eb52681	fc469cbb-4513-46aa-95f1-dd91a2afd199	22ea4cc2-0dcb-4234-8807-f18d1dea7513	\N	tour	DB5EAD	\N	\N	idle	0	2023-09-18 11:34:03	2023-09-23 11:19:03	2023-09-18 11:19:03	2023-09-18 11:19:03
d5f81ac8-8c84-4781-a028-c2730bac25c9	fc469cbb-4513-46aa-95f1-dd91a2afd199	ec7c377d-7164-4dc1-8b4f-a627cb06fde1	\N	tour	E7DFB7	\N	\N	idle	0	2023-09-18 11:34:03	2023-09-23 11:19:03	2023-09-18 11:19:03	2023-09-18 11:19:03
30e9d23e-84e8-45ba-9fbf-02496e867c69	fc469cbb-4513-46aa-95f1-dd91a2afd199	7f3ef76c-e2f7-451d-a22a-d281999f8e93	\N	tour	F3B938	\N	\N	idle	0	2023-09-18 11:34:03	2023-09-23 11:19:03	2023-09-18 11:19:03	2023-09-18 11:19:03
8c486eaa-ba5e-4f6c-b517-ec83e6abcccd	fc469cbb-4513-46aa-95f1-dd91a2afd199	f7e72591-5b34-41e7-aeca-17fa82db67c7	\N	tour	0D54C6	\N	\N	idle	0	2023-09-18 11:34:03	2023-09-23 11:19:03	2023-09-18 11:19:03	2023-09-18 11:19:03
8a399a74-1de2-4862-bd40-447d9afdda49	fc469cbb-4513-46aa-95f1-dd91a2afd199	d76cae1c-551a-4c87-b260-4649c7a7765a	\N	tour	FD20A4	\N	\N	idle	0	2023-09-18 11:34:03	2023-09-23 11:19:03	2023-09-18 11:19:03	2023-09-18 11:19:03
69d87f66-d3e5-471e-b89d-706d014a417e	fc469cbb-4513-46aa-95f1-dd91a2afd199	2b19e6f3-bf24-48c6-af00-52c15cc76bde	\N	tour	835653	\N	\N	idle	0	2023-09-18 11:34:03	2023-09-23 11:19:03	2023-09-18 11:19:03	2023-09-18 11:19:03
7c5a1ba9-5632-462f-8a49-4a9f24f2ef4c	fc469cbb-4513-46aa-95f1-dd91a2afd199	4b1cec5e-f11e-48d6-9238-22e32f76ff8d	\N	tour	37A4CE	\N	\N	idle	0	2023-09-18 11:34:03	2023-09-23 11:19:03	2023-09-18 11:19:03	2023-09-18 11:19:03
b799d2f7-a3ed-4e38-88e7-0065fc1c943d	fc469cbb-4513-46aa-95f1-dd91a2afd199	cb633152-7a48-4f21-814d-40d86c98f882	\N	tour	BFCD50	\N	\N	idle	0	2023-09-18 11:34:03	2023-09-23 11:19:03	2023-09-18 11:19:03	2023-09-18 11:19:03
645366f7-159d-4ad8-8ae1-a3de51388345	fc469cbb-4513-46aa-95f1-dd91a2afd199	11df79cd-789b-4637-a6f7-d0c785d6a3b1	\N	tour	7FF26E	\N	\N	idle	0	2023-09-18 11:34:03	2023-09-23 11:19:03	2023-09-18 11:19:03	2023-09-18 11:19:03
\.


--
-- Data for Name: wards; Type: TABLE DATA; Schema: public; Owner: trekka
--

COPY public.wards (uuid, district_uuid, name, slug, created_at, updated_at) FROM stdin;
\.


--
-- Name: failed_jobs_id_seq; Type: SEQUENCE SET; Schema: public; Owner: trekka
--

SELECT pg_catalog.setval('public.failed_jobs_id_seq', 1, false);


--
-- Name: migrations_id_seq; Type: SEQUENCE SET; Schema: public; Owner: trekka
--

SELECT pg_catalog.setval('public.migrations_id_seq', 75, true);


--
-- Name: oauth_personal_access_clients_id_seq; Type: SEQUENCE SET; Schema: public; Owner: trekka
--

SELECT pg_catalog.setval('public.oauth_personal_access_clients_id_seq', 2, true);


--
-- Name: personal_access_tokens_id_seq; Type: SEQUENCE SET; Schema: public; Owner: trekka
--

SELECT pg_catalog.setval('public.personal_access_tokens_id_seq', 1, false);


--
-- Name: agent_payment_logs agent_payment_logs_pkey; Type: CONSTRAINT; Schema: public; Owner: trekka
--

ALTER TABLE ONLY public.agent_payment_logs
    ADD CONSTRAINT agent_payment_logs_pkey PRIMARY KEY (uuid);


--
-- Name: auth_logs auth_logs_pkey; Type: CONSTRAINT; Schema: public; Owner: trekka
--

ALTER TABLE ONLY public.auth_logs
    ADD CONSTRAINT auth_logs_pkey PRIMARY KEY (uuid);


--
-- Name: campaigns campaigns_pkey; Type: CONSTRAINT; Schema: public; Owner: trekka
--

ALTER TABLE ONLY public.campaigns
    ADD CONSTRAINT campaigns_pkey PRIMARY KEY (uuid);


--
-- Name: connect_packages connect_packages_pkey; Type: CONSTRAINT; Schema: public; Owner: trekka
--

ALTER TABLE ONLY public.connect_packages
    ADD CONSTRAINT connect_packages_pkey PRIMARY KEY (uuid);


--
-- Name: connect_requires connect_requires_pkey; Type: CONSTRAINT; Schema: public; Owner: trekka
--

ALTER TABLE ONLY public.connect_requires
    ADD CONSTRAINT connect_requires_pkey PRIMARY KEY (uuid);


--
-- Name: connected_partners connected_partners_pkey; Type: CONSTRAINT; Schema: public; Owner: trekka
--

ALTER TABLE ONLY public.connected_partners
    ADD CONSTRAINT connected_partners_pkey PRIMARY KEY (uuid);


--
-- Name: conversation_members conversation_members_pkey; Type: CONSTRAINT; Schema: public; Owner: trekka
--

ALTER TABLE ONLY public.conversation_members
    ADD CONSTRAINT conversation_members_pkey PRIMARY KEY (uuid);


--
-- Name: conversations conversations_pkey; Type: CONSTRAINT; Schema: public; Owner: trekka
--

ALTER TABLE ONLY public.conversations
    ADD CONSTRAINT conversations_pkey PRIMARY KEY (uuid);


--
-- Name: countries countries_pkey; Type: CONSTRAINT; Schema: public; Owner: trekka
--

ALTER TABLE ONLY public.countries
    ADD CONSTRAINT countries_pkey PRIMARY KEY (uuid);


--
-- Name: districts districts_pkey; Type: CONSTRAINT; Schema: public; Owner: trekka
--

ALTER TABLE ONLY public.districts
    ADD CONSTRAINT districts_pkey PRIMARY KEY (uuid);


--
-- Name: email_tokens email_tokens_pkey; Type: CONSTRAINT; Schema: public; Owner: trekka
--

ALTER TABLE ONLY public.email_tokens
    ADD CONSTRAINT email_tokens_pkey PRIMARY KEY (uuid);


--
-- Name: failed_jobs failed_jobs_pkey; Type: CONSTRAINT; Schema: public; Owner: trekka
--

ALTER TABLE ONLY public.failed_jobs
    ADD CONSTRAINT failed_jobs_pkey PRIMARY KEY (id);


--
-- Name: failed_jobs failed_jobs_uuid_unique; Type: CONSTRAINT; Schema: public; Owner: trekka
--

ALTER TABLE ONLY public.failed_jobs
    ADD CONSTRAINT failed_jobs_uuid_unique UNIQUE (uuid);


--
-- Name: file_refs file_refs_pkey; Type: CONSTRAINT; Schema: public; Owner: trekka
--

ALTER TABLE ONLY public.file_refs
    ADD CONSTRAINT file_refs_pkey PRIMARY KEY (uuid);


--
-- Name: files files_pkey; Type: CONSTRAINT; Schema: public; Owner: trekka
--

ALTER TABLE ONLY public.files
    ADD CONSTRAINT files_pkey PRIMARY KEY (uuid);


--
-- Name: hobbies hobbies_pkey; Type: CONSTRAINT; Schema: public; Owner: trekka
--

ALTER TABLE ONLY public.hobbies
    ADD CONSTRAINT hobbies_pkey PRIMARY KEY (uuid);


--
-- Name: mbti_details mbti_details_pkey; Type: CONSTRAINT; Schema: public; Owner: trekka
--

ALTER TABLE ONLY public.mbti_details
    ADD CONSTRAINT mbti_details_pkey PRIMARY KEY (uuid);


--
-- Name: mbti_matches mbti_matches_pkey; Type: CONSTRAINT; Schema: public; Owner: trekka
--

ALTER TABLE ONLY public.mbti_matches
    ADD CONSTRAINT mbti_matches_pkey PRIMARY KEY (uuid);


--
-- Name: metadatas metadatas_pkey; Type: CONSTRAINT; Schema: public; Owner: trekka
--

ALTER TABLE ONLY public.metadatas
    ADD CONSTRAINT metadatas_pkey PRIMARY KEY (uuid);


--
-- Name: migrations migrations_pkey; Type: CONSTRAINT; Schema: public; Owner: trekka
--

ALTER TABLE ONLY public.migrations
    ADD CONSTRAINT migrations_pkey PRIMARY KEY (id);


--
-- Name: notices notices_pkey; Type: CONSTRAINT; Schema: public; Owner: trekka
--

ALTER TABLE ONLY public.notices
    ADD CONSTRAINT notices_pkey PRIMARY KEY (uuid);


--
-- Name: oauth_access_tokens oauth_access_tokens_pkey; Type: CONSTRAINT; Schema: public; Owner: trekka
--

ALTER TABLE ONLY public.oauth_access_tokens
    ADD CONSTRAINT oauth_access_tokens_pkey PRIMARY KEY (id);


--
-- Name: oauth_auth_codes oauth_auth_codes_pkey; Type: CONSTRAINT; Schema: public; Owner: trekka
--

ALTER TABLE ONLY public.oauth_auth_codes
    ADD CONSTRAINT oauth_auth_codes_pkey PRIMARY KEY (id);


--
-- Name: oauth_clients oauth_clients_pkey; Type: CONSTRAINT; Schema: public; Owner: trekka
--

ALTER TABLE ONLY public.oauth_clients
    ADD CONSTRAINT oauth_clients_pkey PRIMARY KEY (id);


--
-- Name: oauth_personal_access_clients oauth_personal_access_clients_pkey; Type: CONSTRAINT; Schema: public; Owner: trekka
--

ALTER TABLE ONLY public.oauth_personal_access_clients
    ADD CONSTRAINT oauth_personal_access_clients_pkey PRIMARY KEY (id);


--
-- Name: oauth_refresh_tokens oauth_refresh_tokens_pkey; Type: CONSTRAINT; Schema: public; Owner: trekka
--

ALTER TABLE ONLY public.oauth_refresh_tokens
    ADD CONSTRAINT oauth_refresh_tokens_pkey PRIMARY KEY (id);


--
-- Name: option_datas option_datas_pkey; Type: CONSTRAINT; Schema: public; Owner: trekka
--

ALTER TABLE ONLY public.option_datas
    ADD CONSTRAINT option_datas_pkey PRIMARY KEY (uuid);


--
-- Name: option_groups option_groups_pkey; Type: CONSTRAINT; Schema: public; Owner: trekka
--

ALTER TABLE ONLY public.option_groups
    ADD CONSTRAINT option_groups_pkey PRIMARY KEY (uuid);


--
-- Name: options options_pkey; Type: CONSTRAINT; Schema: public; Owner: trekka
--

ALTER TABLE ONLY public.options
    ADD CONSTRAINT options_pkey PRIMARY KEY (uuid);


--
-- Name: partner_reports partner_reports_pkey; Type: CONSTRAINT; Schema: public; Owner: trekka
--

ALTER TABLE ONLY public.partner_reports
    ADD CONSTRAINT partner_reports_pkey PRIMARY KEY (uuid);


--
-- Name: partner_reviews partner_reviews_pkey; Type: CONSTRAINT; Schema: public; Owner: trekka
--

ALTER TABLE ONLY public.partner_reviews
    ADD CONSTRAINT partner_reviews_pkey PRIMARY KEY (uuid);


--
-- Name: payment_methods payment_methods_pkey; Type: CONSTRAINT; Schema: public; Owner: trekka
--

ALTER TABLE ONLY public.payment_methods
    ADD CONSTRAINT payment_methods_pkey PRIMARY KEY (uuid);


--
-- Name: payment_requests payment_requests_pkey; Type: CONSTRAINT; Schema: public; Owner: trekka
--

ALTER TABLE ONLY public.payment_requests
    ADD CONSTRAINT payment_requests_pkey PRIMARY KEY (uuid);


--
-- Name: payment_transactions payment_transactions_pkey; Type: CONSTRAINT; Schema: public; Owner: trekka
--

ALTER TABLE ONLY public.payment_transactions
    ADD CONSTRAINT payment_transactions_pkey PRIMARY KEY (uuid);


--
-- Name: permission_module_group_actions permission_module_group_actions_pkey; Type: CONSTRAINT; Schema: public; Owner: trekka
--

ALTER TABLE ONLY public.permission_module_group_actions
    ADD CONSTRAINT permission_module_group_actions_pkey PRIMARY KEY (uuid);


--
-- Name: permission_module_roles permission_module_roles_pkey; Type: CONSTRAINT; Schema: public; Owner: trekka
--

ALTER TABLE ONLY public.permission_module_roles
    ADD CONSTRAINT permission_module_roles_pkey PRIMARY KEY (uuid);


--
-- Name: permission_modules permission_modules_pkey; Type: CONSTRAINT; Schema: public; Owner: trekka
--

ALTER TABLE ONLY public.permission_modules
    ADD CONSTRAINT permission_modules_pkey PRIMARY KEY (uuid);


--
-- Name: permission_roles permission_roles_pkey; Type: CONSTRAINT; Schema: public; Owner: trekka
--

ALTER TABLE ONLY public.permission_roles
    ADD CONSTRAINT permission_roles_pkey PRIMARY KEY (uuid);


--
-- Name: permission_user_roles permission_user_roles_pkey; Type: CONSTRAINT; Schema: public; Owner: trekka
--

ALTER TABLE ONLY public.permission_user_roles
    ADD CONSTRAINT permission_user_roles_pkey PRIMARY KEY (uuid);


--
-- Name: personal_access_tokens personal_access_tokens_pkey; Type: CONSTRAINT; Schema: public; Owner: trekka
--

ALTER TABLE ONLY public.personal_access_tokens
    ADD CONSTRAINT personal_access_tokens_pkey PRIMARY KEY (id);


--
-- Name: personal_access_tokens personal_access_tokens_token_unique; Type: CONSTRAINT; Schema: public; Owner: trekka
--

ALTER TABLE ONLY public.personal_access_tokens
    ADD CONSTRAINT personal_access_tokens_token_unique UNIQUE (token);


--
-- Name: places places_pkey; Type: CONSTRAINT; Schema: public; Owner: trekka
--

ALTER TABLE ONLY public.places
    ADD CONSTRAINT places_pkey PRIMARY KEY (uuid);


--
-- Name: posts posts_pkey; Type: CONSTRAINT; Schema: public; Owner: trekka
--

ALTER TABLE ONLY public.posts
    ADD CONSTRAINT posts_pkey PRIMARY KEY (uuid);


--
-- Name: regions regions_pkey; Type: CONSTRAINT; Schema: public; Owner: trekka
--

ALTER TABLE ONLY public.regions
    ADD CONSTRAINT regions_pkey PRIMARY KEY (uuid);


--
-- Name: report_logs report_logs_pkey; Type: CONSTRAINT; Schema: public; Owner: trekka
--

ALTER TABLE ONLY public.report_logs
    ADD CONSTRAINT report_logs_pkey PRIMARY KEY (uuid);


--
-- Name: require_partner_hobbies require_partner_hobbies_pkey; Type: CONSTRAINT; Schema: public; Owner: trekka
--

ALTER TABLE ONLY public.require_partner_hobbies
    ADD CONSTRAINT require_partner_hobbies_pkey PRIMARY KEY (uuid);


--
-- Name: tag_refs tag_refs_pkey; Type: CONSTRAINT; Schema: public; Owner: trekka
--

ALTER TABLE ONLY public.tag_refs
    ADD CONSTRAINT tag_refs_pkey PRIMARY KEY (uuid);


--
-- Name: tags tags_pkey; Type: CONSTRAINT; Schema: public; Owner: trekka
--

ALTER TABLE ONLY public.tags
    ADD CONSTRAINT tags_pkey PRIMARY KEY (uuid);


--
-- Name: trip_points trip_points_pkey; Type: CONSTRAINT; Schema: public; Owner: trekka
--

ALTER TABLE ONLY public.trip_points
    ADD CONSTRAINT trip_points_pkey PRIMARY KEY (uuid);


--
-- Name: user_hobbies user_hobbies_pkey; Type: CONSTRAINT; Schema: public; Owner: trekka
--

ALTER TABLE ONLY public.user_hobbies
    ADD CONSTRAINT user_hobbies_pkey PRIMARY KEY (uuid);


--
-- Name: user_notices user_notices_pkey; Type: CONSTRAINT; Schema: public; Owner: trekka
--

ALTER TABLE ONLY public.user_notices
    ADD CONSTRAINT user_notices_pkey PRIMARY KEY (uuid);


--
-- Name: users users_pkey; Type: CONSTRAINT; Schema: public; Owner: trekka
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_pkey PRIMARY KEY (uuid);


--
-- Name: vouchers vouchers_pkey; Type: CONSTRAINT; Schema: public; Owner: trekka
--

ALTER TABLE ONLY public.vouchers
    ADD CONSTRAINT vouchers_pkey PRIMARY KEY (uuid);


--
-- Name: wards wards_pkey; Type: CONSTRAINT; Schema: public; Owner: trekka
--

ALTER TABLE ONLY public.wards
    ADD CONSTRAINT wards_pkey PRIMARY KEY (uuid);


--
-- Name: mbti_details_mbti_index; Type: INDEX; Schema: public; Owner: trekka
--

CREATE INDEX mbti_details_mbti_index ON public.mbti_details USING btree (mbti);


--
-- Name: mbti_matches_first_mbti_index; Type: INDEX; Schema: public; Owner: trekka
--

CREATE INDEX mbti_matches_first_mbti_index ON public.mbti_matches USING btree (first_mbti);


--
-- Name: mbti_matches_second_mbti_index; Type: INDEX; Schema: public; Owner: trekka
--

CREATE INDEX mbti_matches_second_mbti_index ON public.mbti_matches USING btree (second_mbti);


--
-- Name: oauth_access_tokens_user_id_index; Type: INDEX; Schema: public; Owner: trekka
--

CREATE INDEX oauth_access_tokens_user_id_index ON public.oauth_access_tokens USING btree (user_id);


--
-- Name: oauth_auth_codes_user_id_index; Type: INDEX; Schema: public; Owner: trekka
--

CREATE INDEX oauth_auth_codes_user_id_index ON public.oauth_auth_codes USING btree (user_id);


--
-- Name: oauth_clients_user_id_index; Type: INDEX; Schema: public; Owner: trekka
--

CREATE INDEX oauth_clients_user_id_index ON public.oauth_clients USING btree (user_id);


--
-- Name: oauth_refresh_tokens_access_token_id_index; Type: INDEX; Schema: public; Owner: trekka
--

CREATE INDEX oauth_refresh_tokens_access_token_id_index ON public.oauth_refresh_tokens USING btree (access_token_id);


--
-- Name: password_resets_email_index; Type: INDEX; Schema: public; Owner: trekka
--

CREATE INDEX password_resets_email_index ON public.password_resets USING btree (email);


--
-- Name: personal_access_tokens_tokenable_type_tokenable_id_index; Type: INDEX; Schema: public; Owner: trekka
--

CREATE INDEX personal_access_tokens_tokenable_type_tokenable_id_index ON public.personal_access_tokens USING btree (tokenable_type, tokenable_id);


--
-- PostgreSQL database dump complete
--

