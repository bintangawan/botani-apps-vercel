-- Indexes aligned with the application's existing public catalog, module,
-- media, and relationship lookups. They do not alter stored data or RLS.
create index if not exists plant_species_featured_idx
  on public.plant_species (status, updated_at desc);

create index if not exists learning_modules_public_idx
  on public.learning_modules (status, module_order);

create index if not exists species_taxa_taxon_idx
  on public.species_taxa (taxon_id, species_id);

create index if not exists locations_regency_idx
  on public.locations (regency);

create index if not exists plant_observations_location_idx
  on public.plant_observations (location_id, species_id);

create index if not exists media_species_primary_idx
  on public.media (species_id, is_primary desc);

create index if not exists module_species_species_idx
  on public.module_species (species_id, module_id);

create index if not exists question_options_question_idx
  on public.question_options (question_id);
