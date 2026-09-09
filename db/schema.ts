import { sql } from "drizzle-orm";
import {
  bigint,
  boolean,
  check,
  date,
  index,
  integer,
  jsonb,
  pgEnum,
  pgTable,
  primaryKey,
  text,
  timestamp,
  unique,
  uniqueIndex,
  uuid,
  type AnyPgColumn,
} from "drizzle-orm/pg-core";

export const userRoleEnum = pgEnum("user_role", ["mahasiswa", "dosen", "admin"]);
export const accountStatusEnum = pgEnum("account_status", ["active", "inactive"]);
export const publicationStatusEnum = pgEnum("publication_status", ["draft", "published"]);
export const groupTypeEnum = pgEnum("group_type", ["Gymnospermae", "Angiospermae"]);
export const cotyledonTypeEnum = pgEnum("cotyledon_type", ["Monokotil", "Dikotil", "Tidak Berlaku"]);
export const taxonRankEnum = pgEnum("taxon_rank", ["kingdom", "divisi", "kelas", "ordo", "famili", "genus", "spesies"]);
export const mediaTypeEnum = pgEnum("media_type", ["image", "diagram", "video"]);
export const quizTypeEnum = pgEnum("quiz_type", ["pilihan_ganda", "esai", "campuran", "pretest", "practice", "posttest"]);
export const questionTypeEnum = pgEnum("question_type", ["pilihan_ganda", "esai", "multiple_choice", "true_false"]);

export const appSettings = pgTable("app_settings", {
  key: text("key").primaryKey(),
  value: text("value").notNull(),
  updatedAt: timestamp("updated_at", { withTimezone: true, mode: "string" }).notNull().defaultNow(),
});

export const systemHeartbeat = pgTable("system_heartbeat", {
  id: boolean("id").primaryKey().default(true),
  lastSeen: timestamp("last_seen", { withTimezone: true, mode: "string" }).notNull().defaultNow(),
  source: text("source").notNull(),
  hitCount: bigint("hit_count", { mode: "number" }).notNull().default(1),
}, (table) => [
  check("system_heartbeat_singleton", sql`${table.id} = true`),
  check("system_heartbeat_positive_hits", sql`${table.hitCount} > 0`),
]);

export const profiles = pgTable("profiles", {
  // The canonical Supabase migration adds the foreign key to auth.users(id).
  id: uuid("id").primaryKey(),
  name: text("name").notNull(),
  email: text("email").notNull().unique(),
  role: userRoleEnum("role").notNull().default("mahasiswa"),
  institution: text("institution"),
  status: accountStatusEnum("status").notNull().default("active"),
  createdAt: timestamp("created_at", { withTimezone: true, mode: "string" }).notNull().defaultNow(),
  updatedAt: timestamp("updated_at", { withTimezone: true, mode: "string" }).notNull().defaultNow(),
});

export const plantSpecies = pgTable(
  "plant_species",
  {
    id: bigint("id", { mode: "number" }).primaryKey().generatedByDefaultAsIdentity(),
    code: text("code").notNull().unique(),
    slug: text("slug").notNull().unique(),
    localName: text("local_name").notNull(),
    scientificName: text("scientific_name").notNull(),
    authorName: text("author_name"),
    groupType: groupTypeEnum("group_type").notNull(),
    cotyledonType: cotyledonTypeEnum("cotyledon_type").notNull(),
    description: text("description"),
    habitat: text("habitat"),
    benefits: text("benefits"),
    imagePath: text("image_path"),
    status: publicationStatusEnum("status").notNull().default("published"),
    createdAt: timestamp("created_at", { withTimezone: true, mode: "string" }).notNull().defaultNow(),
    updatedAt: timestamp("updated_at", { withTimezone: true, mode: "string" }).notNull().defaultNow(),
  },
  (table) => [index("plant_species_catalog_idx").on(table.status, table.groupType, table.cotyledonType, table.localName)],
);

export const taxa = pgTable(
  "taxa",
  {
    id: bigint("id", { mode: "number" }).primaryKey().generatedByDefaultAsIdentity(),
    parentId: bigint("parent_id", { mode: "number" }).references((): AnyPgColumn => taxa.id, { onDelete: "set null" }),
    name: text("name").notNull(),
    rank: taxonRankEnum("rank").notNull(),
    createdAt: timestamp("created_at", { withTimezone: true, mode: "string" }).notNull().defaultNow(),
    updatedAt: timestamp("updated_at", { withTimezone: true, mode: "string" }).notNull().defaultNow(),
  },
  (table) => [
    unique("taxa_rank_name_unique").on(table.rank, table.name),
    index("taxa_rank_name_idx").on(table.rank, table.name),
  ],
);

export const speciesTaxa = pgTable(
  "species_taxa",
  {
    speciesId: bigint("species_id", { mode: "number" }).notNull().references(() => plantSpecies.id, { onDelete: "cascade" }),
    taxonId: bigint("taxon_id", { mode: "number" }).notNull().references(() => taxa.id, { onDelete: "cascade" }),
    createdAt: timestamp("created_at", { withTimezone: true, mode: "string" }).notNull().defaultNow(),
  },
  (table) => [primaryKey({ columns: [table.speciesId, table.taxonId] })],
);

export const morphologies = pgTable("morphologies", {
  speciesId: bigint("species_id", { mode: "number" }).primaryKey().references(() => plantSpecies.id, { onDelete: "cascade" }),
  root: text("root"),
  stem: text("stem"),
  leaf: text("leaf"),
  flower: text("flower"),
  fruit: text("fruit"),
  seed: text("seed"),
  specialCharacteristics: text("special_characteristics"),
  createdAt: timestamp("created_at", { withTimezone: true, mode: "string" }).notNull().defaultNow(),
  updatedAt: timestamp("updated_at", { withTimezone: true, mode: "string" }).notNull().defaultNow(),
});

export const physiologies = pgTable("physiologies", {
  speciesId: bigint("species_id", { mode: "number" }).primaryKey().references(() => plantSpecies.id, { onDelete: "cascade" }),
  reproduction: text("reproduction"),
  growth: text("growth"),
  adaptation: text("adaptation"),
  additionalInformation: text("additional_information"),
  createdAt: timestamp("created_at", { withTimezone: true, mode: "string" }).notNull().defaultNow(),
  updatedAt: timestamp("updated_at", { withTimezone: true, mode: "string" }).notNull().defaultNow(),
});

export const locations = pgTable(
  "locations",
  {
    id: bigint("id", { mode: "number" }).primaryKey().generatedByDefaultAsIdentity(),
    locationName: text("location_name").notNull(),
    village: text("village"),
    district: text("district").notNull(),
    regency: text("regency").notNull(),
    province: text("province").notNull(),
    createdAt: timestamp("created_at", { withTimezone: true, mode: "string" }).notNull().defaultNow(),
    updatedAt: timestamp("updated_at", { withTimezone: true, mode: "string" }).notNull().defaultNow(),
  },
  (table) => [unique("locations_district_regency_province_unique").on(table.district, table.regency, table.province)],
);

export const plantObservations = pgTable(
  "plant_observations",
  {
    id: bigint("id", { mode: "number" }).primaryKey().generatedByDefaultAsIdentity(),
    speciesId: bigint("species_id", { mode: "number" }).notNull().references(() => plantSpecies.id, { onDelete: "cascade" }),
    locationId: bigint("location_id", { mode: "number" }).notNull().references(() => locations.id, { onDelete: "cascade" }),
    observerId: uuid("observer_id").references(() => profiles.id, { onDelete: "set null" }),
    observationDate: date("observation_date", { mode: "string" }),
    notes: text("notes"),
    createdAt: timestamp("created_at", { withTimezone: true, mode: "string" }).notNull().defaultNow(),
    updatedAt: timestamp("updated_at", { withTimezone: true, mode: "string" }).notNull().defaultNow(),
  },
  (table) => [
    unique("plant_observations_species_location_unique").on(table.speciesId, table.locationId),
    index("plant_observations_species_idx").on(table.speciesId, table.observationDate),
  ],
);

export const media = pgTable(
  "media",
  {
    id: bigint("id", { mode: "number" }).primaryKey().generatedByDefaultAsIdentity(),
    speciesId: bigint("species_id", { mode: "number" }).references(() => plantSpecies.id, { onDelete: "cascade" }),
    observationId: bigint("observation_id", { mode: "number" }).references(() => plantObservations.id, { onDelete: "cascade" }),
    filename: text("filename").notNull(),
    filePath: text("file_path").notNull(),
    mediaType: mediaTypeEnum("media_type").notNull().default("image"),
    caption: text("caption"),
    isPrimary: boolean("is_primary").notNull().default(false),
    createdAt: timestamp("created_at", { withTimezone: true, mode: "string" }).notNull().defaultNow(),
    updatedAt: timestamp("updated_at", { withTimezone: true, mode: "string" }).notNull().defaultNow(),
  },
  (table) => [
    check("media_has_owner_check", sql`${table.speciesId} is not null or ${table.observationId} is not null`),
    unique("media_species_file_path_unique").on(table.speciesId, table.filePath),
  ],
);

export const learningModules = pgTable(
  "learning_modules",
  {
    id: bigint("id", { mode: "number" }).primaryKey().generatedByDefaultAsIdentity(),
    title: text("title").notNull(),
    slug: text("slug").notNull().unique(),
    description: text("description").notNull().default(""),
    moduleOrder: integer("module_order").notNull().default(1),
    estimatedMinutes: integer("estimated_minutes").notNull().default(0),
    chapterSummary: jsonb("chapter_summary").$type<string[]>().notNull().default([]),
    sourceFile: text("source_file"),
    status: publicationStatusEnum("status").notNull().default("published"),
    createdAt: timestamp("created_at", { withTimezone: true, mode: "string" }).notNull().defaultNow(),
    updatedAt: timestamp("updated_at", { withTimezone: true, mode: "string" }).notNull().defaultNow(),
  },
  (table) => [
    unique("learning_modules_order_unique").on(table.moduleOrder),
    check("learning_modules_order_check", sql`${table.moduleOrder} > 0`),
    check("learning_modules_minutes_check", sql`${table.estimatedMinutes} >= 0`),
  ],
);

export const learningLessons = pgTable(
  "learning_lessons",
  {
    id: bigint("id", { mode: "number" }).primaryKey().generatedByDefaultAsIdentity(),
    moduleId: bigint("module_id", { mode: "number" }).notNull().references(() => learningModules.id, { onDelete: "cascade" }),
    sourceId: text("source_id").unique(),
    title: text("title").notNull(),
    slug: text("slug").notNull(),
    content: text("content").notNull(),
    contentFormat: text("content_format").notNull().default("markdown"),
    keyPoints: jsonb("key_points").$type<string[]>().notNull().default([]),
    sourceSections: jsonb("source_sections").$type<string[]>().notNull().default([]),
    lessonOrder: integer("lesson_order").notNull().default(1),
    createdAt: timestamp("created_at", { withTimezone: true, mode: "string" }).notNull().defaultNow(),
    updatedAt: timestamp("updated_at", { withTimezone: true, mode: "string" }).notNull().defaultNow(),
  },
  (table) => [
    unique("learning_lessons_module_slug_unique").on(table.moduleId, table.slug),
    unique("learning_lessons_module_order_unique").on(table.moduleId, table.lessonOrder),
    check("learning_lessons_order_check", sql`${table.lessonOrder} > 0`),
    index("learning_lessons_module_idx").on(table.moduleId, table.lessonOrder),
  ],
);

export const moduleSpecies = pgTable(
  "module_species",
  {
    moduleId: bigint("module_id", { mode: "number" }).notNull().references(() => learningModules.id, { onDelete: "cascade" }),
    speciesId: bigint("species_id", { mode: "number" }).notNull().references(() => plantSpecies.id, { onDelete: "cascade" }),
    createdAt: timestamp("created_at", { withTimezone: true, mode: "string" }).notNull().defaultNow(),
  },
  (table) => [primaryKey({ columns: [table.moduleId, table.speciesId] })],
);

export const quizzes = pgTable(
  "quizzes",
  {
    id: bigint("id", { mode: "number" }).primaryKey().generatedByDefaultAsIdentity(),
    moduleId: bigint("module_id", { mode: "number" }).notNull().references(() => learningModules.id, { onDelete: "cascade" }),
    title: text("title").notNull(),
    quizType: quizTypeEnum("quiz_type").notNull().default("pilihan_ganda"),
    passingScore: integer("passing_score").notNull().default(60),
    duration: integer("duration").notNull().default(30),
    status: publicationStatusEnum("status").notNull().default("published"),
    createdAt: timestamp("created_at", { withTimezone: true, mode: "string" }).notNull().defaultNow(),
    updatedAt: timestamp("updated_at", { withTimezone: true, mode: "string" }).notNull().defaultNow(),
  },
  (table) => [
    check("quizzes_passing_score_check", sql`${table.passingScore} between 0 and 100`),
    check("quizzes_duration_check", sql`${table.duration} between 1 and 360`),
    index("quizzes_module_idx").on(table.moduleId, table.status),
  ],
);

export const questions = pgTable(
  "questions",
  {
    id: bigint("id", { mode: "number" }).primaryKey().generatedByDefaultAsIdentity(),
    quizId: bigint("quiz_id", { mode: "number" }).notNull().references(() => quizzes.id, { onDelete: "cascade" }),
    questionText: text("question_text").notNull(),
    questionType: questionTypeEnum("question_type").notNull().default("pilihan_ganda"),
    scoreWeight: integer("score_weight").notNull().default(1),
    createdAt: timestamp("created_at", { withTimezone: true, mode: "string" }).notNull().defaultNow(),
    updatedAt: timestamp("updated_at", { withTimezone: true, mode: "string" }).notNull().defaultNow(),
  },
  (table) => [
    check("questions_score_weight_check", sql`${table.scoreWeight} between 1 and 100`),
    index("questions_quiz_idx").on(table.quizId),
  ],
);

export const questionOptions = pgTable("question_options", {
  id: bigint("id", { mode: "number" }).primaryKey().generatedByDefaultAsIdentity(),
  questionId: bigint("question_id", { mode: "number" }).notNull().references(() => questions.id, { onDelete: "cascade" }),
  optionText: text("option_text").notNull(),
  createdAt: timestamp("created_at", { withTimezone: true, mode: "string" }).notNull().defaultNow(),
  updatedAt: timestamp("updated_at", { withTimezone: true, mode: "string" }).notNull().defaultNow(),
});

export const questionKeys = pgTable("question_keys", {
  questionId: bigint("question_id", { mode: "number" }).primaryKey().references(() => questions.id, { onDelete: "cascade" }),
  correctOptionId: bigint("correct_option_id", { mode: "number" }).notNull().references(() => questionOptions.id, { onDelete: "cascade" }),
  createdAt: timestamp("created_at", { withTimezone: true, mode: "string" }).notNull().defaultNow(),
  updatedAt: timestamp("updated_at", { withTimezone: true, mode: "string" }).notNull().defaultNow(),
});

export const quizAttempts = pgTable(
  "quiz_attempts",
  {
    id: bigint("id", { mode: "number" }).primaryKey().generatedByDefaultAsIdentity(),
    userId: uuid("user_id").notNull().references(() => profiles.id, { onDelete: "cascade" }),
    quizId: bigint("quiz_id", { mode: "number" }).notNull().references(() => quizzes.id, { onDelete: "cascade" }),
    startedAt: timestamp("started_at", { withTimezone: true, mode: "string" }).notNull().defaultNow(),
    completedAt: timestamp("completed_at", { withTimezone: true, mode: "string" }),
    score: integer("score"),
    totalCorrect: integer("total_correct").notNull().default(0),
    createdAt: timestamp("created_at", { withTimezone: true, mode: "string" }).notNull().defaultNow(),
    updatedAt: timestamp("updated_at", { withTimezone: true, mode: "string" }).notNull().defaultNow(),
  },
  (table) => [
    check("quiz_attempts_score_check", sql`${table.score} between 0 and 100`),
    check("quiz_attempts_total_correct_check", sql`${table.totalCorrect} >= 0`),
    uniqueIndex("quiz_attempts_one_active_idx").on(table.userId, table.quizId).where(sql`${table.completedAt} is null`),
    index("quiz_attempts_user_idx").on(table.userId, table.createdAt),
  ],
);

export const quizAnswers = pgTable(
  "quiz_answers",
  {
    id: bigint("id", { mode: "number" }).primaryKey().generatedByDefaultAsIdentity(),
    attemptId: bigint("attempt_id", { mode: "number" }).notNull().references(() => quizAttempts.id, { onDelete: "cascade" }),
    questionId: bigint("question_id", { mode: "number" }).notNull().references(() => questions.id, { onDelete: "cascade" }),
    selectedOptionId: bigint("selected_option_id", { mode: "number" }).references(() => questionOptions.id, { onDelete: "set null" }),
    essayAnswer: text("essay_answer"),
    isCorrect: boolean("is_correct").notNull().default(false),
    createdAt: timestamp("created_at", { withTimezone: true, mode: "string" }).notNull().defaultNow(),
    updatedAt: timestamp("updated_at", { withTimezone: true, mode: "string" }).notNull().defaultNow(),
  },
  (table) => [unique("quiz_answers_attempt_question_unique").on(table.attemptId, table.questionId)],
);

export type Profile = typeof profiles.$inferSelect;
export type NewProfile = typeof profiles.$inferInsert;
