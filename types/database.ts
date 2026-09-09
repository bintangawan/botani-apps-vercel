export type Json =
  | string
  | number
  | boolean
  | null
  | { [key: string]: Json | undefined }
  | Json[];

export type UserRole = "mahasiswa" | "dosen" | "admin";
export type AccountStatus = "active" | "inactive";
export type PublicationStatus = "draft" | "published";
export type GroupType = "Gymnospermae" | "Angiospermae";
export type CotyledonType = "Monokotil" | "Dikotil" | "Tidak Berlaku";
export type TaxonRank = "kingdom" | "divisi" | "kelas" | "ordo" | "famili" | "genus" | "spesies";
export type MediaType = "image" | "diagram" | "video";
export type QuizType = "pilihan_ganda" | "esai" | "campuran" | "pretest" | "practice" | "posttest";
export type QuestionType = "pilihan_ganda" | "esai" | "multiple_choice" | "true_false";

type Timestamped = {
  created_at: string;
  updated_at: string;
};

type TableDefinition<Row, Insert, Update> = {
  Row: Row;
  Insert: Insert;
  Update: Update;
  Relationships: [];
};

export type Database = {
  public: {
    Tables: {
      app_settings: TableDefinition<
        { key: string; value: string; updated_at: string },
        { key: string; value: string; updated_at?: string },
        { value?: string; updated_at?: string }
      >;
      profiles: TableDefinition<
        Timestamped & { id: string; name: string; email: string; role: UserRole; institution: string | null; status: AccountStatus },
        { id: string; name: string; email: string; role?: UserRole; institution?: string | null; status?: AccountStatus; created_at?: string; updated_at?: string },
        { name?: string; email?: string; role?: UserRole; institution?: string | null; status?: AccountStatus; updated_at?: string }
      >;
      plant_species: TableDefinition<
        Timestamped & { id: number; code: string; slug: string; local_name: string; scientific_name: string; author_name: string | null; group_type: GroupType; cotyledon_type: CotyledonType; description: string | null; habitat: string | null; benefits: string | null; image_path: string | null; status: PublicationStatus },
        { id?: number; code: string; slug: string; local_name: string; scientific_name: string; author_name?: string | null; group_type: GroupType; cotyledon_type: CotyledonType; description?: string | null; habitat?: string | null; benefits?: string | null; image_path?: string | null; status?: PublicationStatus; created_at?: string; updated_at?: string },
        { code?: string; slug?: string; local_name?: string; scientific_name?: string; author_name?: string | null; group_type?: GroupType; cotyledon_type?: CotyledonType; description?: string | null; habitat?: string | null; benefits?: string | null; image_path?: string | null; status?: PublicationStatus; updated_at?: string }
      >;
      taxa: TableDefinition<
        Timestamped & { id: number; parent_id: number | null; name: string; rank: TaxonRank },
        { id?: number; parent_id?: number | null; name: string; rank: TaxonRank; created_at?: string; updated_at?: string },
        { parent_id?: number | null; name?: string; rank?: TaxonRank; updated_at?: string }
      >;
      species_taxa: TableDefinition<
        { species_id: number; taxon_id: number; created_at: string },
        { species_id: number; taxon_id: number; created_at?: string },
        { species_id?: number; taxon_id?: number }
      >;
      morphologies: TableDefinition<
        Timestamped & { species_id: number; root: string | null; stem: string | null; leaf: string | null; flower: string | null; fruit: string | null; seed: string | null; special_characteristics: string | null },
        { species_id: number; root?: string | null; stem?: string | null; leaf?: string | null; flower?: string | null; fruit?: string | null; seed?: string | null; special_characteristics?: string | null; created_at?: string; updated_at?: string },
        { root?: string | null; stem?: string | null; leaf?: string | null; flower?: string | null; fruit?: string | null; seed?: string | null; special_characteristics?: string | null; updated_at?: string }
      >;
      physiologies: TableDefinition<
        Timestamped & { species_id: number; reproduction: string | null; growth: string | null; adaptation: string | null; additional_information: string | null },
        { species_id: number; reproduction?: string | null; growth?: string | null; adaptation?: string | null; additional_information?: string | null; created_at?: string; updated_at?: string },
        { reproduction?: string | null; growth?: string | null; adaptation?: string | null; additional_information?: string | null; updated_at?: string }
      >;
      locations: TableDefinition<
        Timestamped & { id: number; location_name: string; village: string | null; district: string; regency: string; province: string },
        { id?: number; location_name: string; village?: string | null; district: string; regency: string; province: string; created_at?: string; updated_at?: string },
        { location_name?: string; village?: string | null; district?: string; regency?: string; province?: string; updated_at?: string }
      >;
      plant_observations: TableDefinition<
        Timestamped & { id: number; species_id: number; location_id: number; observer_id: string | null; observation_date: string | null; notes: string | null },
        { id?: number; species_id: number; location_id: number; observer_id?: string | null; observation_date?: string | null; notes?: string | null; created_at?: string; updated_at?: string },
        { species_id?: number; location_id?: number; observer_id?: string | null; observation_date?: string | null; notes?: string | null; updated_at?: string }
      >;
      media: TableDefinition<
        Timestamped & { id: number; species_id: number | null; observation_id: number | null; filename: string; file_path: string; media_type: MediaType; caption: string | null; is_primary: boolean },
        { id?: number; species_id?: number | null; observation_id?: number | null; filename: string; file_path: string; media_type?: MediaType; caption?: string | null; is_primary?: boolean; created_at?: string; updated_at?: string },
        { species_id?: number | null; observation_id?: number | null; filename?: string; file_path?: string; media_type?: MediaType; caption?: string | null; is_primary?: boolean; updated_at?: string }
      >;
      learning_modules: TableDefinition<
        Timestamped & { id: number; title: string; slug: string; description: string; module_order: number; estimated_minutes: number; chapter_summary: Json; source_file: string | null; status: PublicationStatus },
        { id?: number; title: string; slug: string; description?: string; module_order?: number; estimated_minutes?: number; chapter_summary?: Json; source_file?: string | null; status?: PublicationStatus; created_at?: string; updated_at?: string },
        { title?: string; slug?: string; description?: string; module_order?: number; estimated_minutes?: number; chapter_summary?: Json; source_file?: string | null; status?: PublicationStatus; updated_at?: string }
      >;
      learning_lessons: TableDefinition<
        Timestamped & { id: number; module_id: number; source_id: string | null; title: string; slug: string; content: string; content_format: string; key_points: Json; source_sections: Json; lesson_order: number },
        { id?: number; module_id: number; source_id?: string | null; title: string; slug: string; content: string; content_format?: string; key_points?: Json; source_sections?: Json; lesson_order?: number; created_at?: string; updated_at?: string },
        { module_id?: number; source_id?: string | null; title?: string; slug?: string; content?: string; content_format?: string; key_points?: Json; source_sections?: Json; lesson_order?: number; updated_at?: string }
      >;
      module_species: TableDefinition<
        { module_id: number; species_id: number; created_at: string },
        { module_id: number; species_id: number; created_at?: string },
        { module_id?: number; species_id?: number }
      >;
      quizzes: TableDefinition<
        Timestamped & { id: number; module_id: number; title: string; quiz_type: QuizType; passing_score: number; duration: number; status: PublicationStatus },
        { id?: number; module_id: number; title: string; quiz_type?: QuizType; passing_score?: number; duration?: number; status?: PublicationStatus; created_at?: string; updated_at?: string },
        { module_id?: number; title?: string; quiz_type?: QuizType; passing_score?: number; duration?: number; status?: PublicationStatus; updated_at?: string }
      >;
      questions: TableDefinition<
        Timestamped & { id: number; quiz_id: number; question_text: string; question_type: QuestionType; score_weight: number },
        { id?: number; quiz_id: number; question_text: string; question_type?: QuestionType; score_weight?: number; created_at?: string; updated_at?: string },
        { quiz_id?: number; question_text?: string; question_type?: QuestionType; score_weight?: number; updated_at?: string }
      >;
      question_options: TableDefinition<
        Timestamped & { id: number; question_id: number; option_text: string },
        { id?: number; question_id: number; option_text: string; created_at?: string; updated_at?: string },
        { question_id?: number; option_text?: string; updated_at?: string }
      >;
      question_keys: TableDefinition<
        Timestamped & { question_id: number; correct_option_id: number },
        { question_id: number; correct_option_id: number; created_at?: string; updated_at?: string },
        { correct_option_id?: number; updated_at?: string }
      >;
      quiz_attempts: TableDefinition<
        Timestamped & { id: number; user_id: string; quiz_id: number; started_at: string; completed_at: string | null; score: number | null; total_correct: number },
        { id?: number; user_id: string; quiz_id: number; started_at?: string; completed_at?: string | null; score?: number | null; total_correct?: number; created_at?: string; updated_at?: string },
        { completed_at?: string | null; score?: number | null; total_correct?: number; updated_at?: string }
      >;
      quiz_answers: TableDefinition<
        Timestamped & { id: number; attempt_id: number; question_id: number; selected_option_id: number | null; essay_answer: string | null; is_correct: boolean },
        { id?: number; attempt_id: number; question_id: number; selected_option_id?: number | null; essay_answer?: string | null; is_correct?: boolean; created_at?: string; updated_at?: string },
        { selected_option_id?: number | null; essay_answer?: string | null; is_correct?: boolean; updated_at?: string }
      >;
    };
    Views: Record<string, never>;
    Functions: {
      current_user_role: { Args: Record<PropertyKey, never>; Returns: UserRole | null };
      get_catalog_statistics: {
        Args: Record<PropertyKey, never>;
        Returns: { total_species: number; gymnospermae: number; angiospermae: number }[];
      };
      get_published_modules_with_counts: {
        Args: Record<PropertyKey, never>;
        Returns: (Timestamped & {
          id: number;
          title: string;
          slug: string;
          description: string;
          module_order: number;
          estimated_minutes: number;
          chapter_summary: Json;
          source_file: string | null;
          status: PublicationStatus;
          lesson_count: number;
          quiz_count: number;
          species_count: number;
        })[];
      };
      get_quiz_result: { Args: { p_attempt_id: number }; Returns: Json };
      save_learning_module: { Args: { p_module_id: number | null; p_payload: Json; p_lessons: Json; p_species_ids: number[] }; Returns: number };
      save_plant_species: { Args: { p_plant_id: number | null; p_payload: Json; p_morphology: Json; p_taxonomy: Json }; Returns: number };
      start_quiz_attempt: { Args: { p_quiz_id: number }; Returns: number };
      submit_quiz_attempt: { Args: { p_attempt_id: number; p_answers: Json }; Returns: Json };
    };
    Enums: {
      account_status: AccountStatus;
      cotyledon_type: CotyledonType;
      group_type: GroupType;
      media_type: MediaType;
      publication_status: PublicationStatus;
      question_type: QuestionType;
      quiz_type: QuizType;
      taxon_rank: TaxonRank;
      user_role: UserRole;
    };
    CompositeTypes: Record<string, never>;
  };
};

export type TableName = keyof Database["public"]["Tables"];
export type Tables<Name extends TableName> = Database["public"]["Tables"][Name]["Row"];
export type TablesInsert<Name extends TableName> = Database["public"]["Tables"][Name]["Insert"];
export type TablesUpdate<Name extends TableName> = Database["public"]["Tables"][Name]["Update"];
