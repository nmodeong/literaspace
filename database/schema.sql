-- Jalankan di Supabase SQL Editor sebelum memakai aplikasi.
create table if not exists users (
    id bigserial primary key,
    name varchar(120) not null,
    email varchar(150) unique not null,
    password varchar(255) not null,
    phone varchar(30),
    address text,
    photo varchar(255),
    role varchar(20) not null default 'member',
    is_active boolean not null default true,
    created_at timestamp not null default current_timestamp
);

create table if not exists categories (
    id bigserial primary key,
    name varchar(100) unique not null
);

create table if not exists books (
    id bigserial primary key,
    category_id bigint references categories(id) on delete set null,
    title varchar(180) not null,
    author varchar(140) not null,
    publisher varchar(140),
    year int,
    isbn varchar(80),
    description text,
    cover varchar(255),
    stock int not null default 1,
    available_stock int not null default 1,
    created_at timestamp not null default current_timestamp
);

create table if not exists loans (
    id bigserial primary key,
    user_id bigint not null references users(id) on delete cascade,
    book_id bigint not null references books(id) on delete cascade,
    loan_date date not null default current_date,
    due_date date not null,
    return_date date,
    status varchar(30) not null default 'pending',
    fine int not null default 0,
    created_at timestamp not null default current_timestamp
);

insert into categories (name)
values ('Fiksi'), ('Nonfiksi'), ('Teknologi'), ('Sejarah')
on conflict (name) do nothing;

-- Akun admin awal: email admin@perpustakaan.test, password admin123
insert into users (name, email, password, role, is_active)
values ('Admin Perpustakaan', 'admin@perpustakaan.test', '$2y$10$pGItflq8ry6GZNrdBnWzB.CEoZeMU56AJzUd/L.7M6N4TMog8ESbG', 'admin', true)
on conflict (email) do nothing;
