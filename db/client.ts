import "server-only";

import { drizzle, type PostgresJsDatabase } from "drizzle-orm/postgres-js";
import postgres, { type Sql } from "postgres";
import { getDatabaseEnv } from "@/lib/env";
import * as schema from "@/db/schema";

type Database = PostgresJsDatabase<typeof schema>;
type DatabaseConnection = {
  db: Database;
  client: Sql;
};

const globalDatabase = globalThis as typeof globalThis & {
  botaniDatabase?: DatabaseConnection;
};

function createConnection(): DatabaseConnection {
  const { DATABASE_URL } = getDatabaseEnv();
  const client = postgres(DATABASE_URL, {
    max: 1,
    prepare: false,
  });

  return {
    client,
    db: drizzle(client, { schema }),
  };
}

export function getDatabase(): Database {
  globalDatabase.botaniDatabase ??= createConnection();
  return globalDatabase.botaniDatabase.db;
}
