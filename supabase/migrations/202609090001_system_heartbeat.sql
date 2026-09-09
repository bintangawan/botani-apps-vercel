begin;

create extension if not exists pg_cron;
create schema if not exists botani_internal;
revoke all on schema botani_internal from public, anon, authenticated;

create table public.system_heartbeat (
  id boolean primary key default true,
  last_seen timestamptz not null default now(),
  source text not null,
  hit_count bigint not null default 1,
  constraint system_heartbeat_singleton check (id = true),
  constraint system_heartbeat_positive_hits check (hit_count > 0)
);

-- Access is through the server's database connection, never browser/PostgREST roles.
alter table public.system_heartbeat enable row level security;
revoke all on table public.system_heartbeat from public, anon, authenticated;

create function botani_internal.record_heartbeat()
returns void
language sql
security invoker
set search_path = ''
set statement_timeout = '15s'
as $$
  insert into public.system_heartbeat (id, last_seen, source, hit_count)
  values (true, now(), 'supabase-pg-cron', 1)
  on conflict (id) do update
    set last_seen = now(),
        source = excluded.source,
        hit_count = public.system_heartbeat.hit_count + 1;
$$;

revoke all on function botani_internal.record_heartbeat() from public, anon, authenticated;

-- Supabase pg_cron uses GMT/UTC: 17:00 UTC = 00:00 Asia/Jakarta.
-- Fail rather than silently schedule the wrong local hour on a customized server.
do $$
begin
  if coalesce(current_setting('cron.timezone', true), 'GMT') not in ('GMT', 'UTC', 'Etc/UTC') then
    raise exception 'Cron timezone must be UTC/GMT for the midnight WIB schedule';
  end if;
end;
$$;

select cron.schedule(
  'botani-daily-heartbeat',
  '0 17 * * *',
  'select botani_internal.record_heartbeat();'
);

commit;
