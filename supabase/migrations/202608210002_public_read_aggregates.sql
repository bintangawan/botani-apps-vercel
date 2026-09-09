create or replace function public.get_catalog_statistics()
returns table (
  total_species bigint,
  gymnospermae bigint,
  angiospermae bigint
)
language sql
stable
security invoker
set search_path = ''
as $$
  select
    count(*) as total_species,
    count(*) filter (where group_type = 'Gymnospermae'::public.group_type) as gymnospermae,
    count(*) filter (where group_type = 'Angiospermae'::public.group_type) as angiospermae
  from public.plant_species
  where status = 'published'::public.publication_status;
$$;

create or replace function public.get_published_modules_with_counts()
returns table (
  id bigint,
  title text,
  slug text,
  description text,
  module_order integer,
  estimated_minutes integer,
  chapter_summary jsonb,
  source_file text,
  status public.publication_status,
  created_at timestamptz,
  updated_at timestamptz,
  lesson_count bigint,
  quiz_count bigint,
  species_count bigint
)
language sql
stable
security invoker
set search_path = ''
as $$
  select
    module.id,
    module.title,
    module.slug,
    module.description,
    module.module_order,
    module.estimated_minutes,
    module.chapter_summary,
    module.source_file,
    module.status,
    module.created_at,
    module.updated_at,
    (select count(*) from public.learning_lessons lesson where lesson.module_id = module.id) as lesson_count,
    (select count(*) from public.quizzes quiz where quiz.module_id = module.id) as quiz_count,
    (select count(*) from public.module_species relation where relation.module_id = module.id) as species_count
  from public.learning_modules module
  where module.status = 'published'::public.publication_status
  order by module.module_order;
$$;

grant execute on function public.get_catalog_statistics() to anon, authenticated;
grant execute on function public.get_published_modules_with_counts() to anon, authenticated;
