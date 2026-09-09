import { authRouter } from "@/server/api/routers/auth";
import { plantsRouter } from "@/server/api/routers/plants";
import { modulesRouter } from "@/server/api/routers/modules";
import { quizzesRouter } from "@/server/api/routers/quizzes";
import { dashboardRouter } from "@/server/api/routers/dashboard";
import { usersRouter } from "@/server/api/routers/users";
import { createCallerFactory, createTRPCRouter } from "@/server/api/trpc";

export const appRouter = createTRPCRouter({
  auth: authRouter,
  plants: plantsRouter,
  modules: modulesRouter,
  quizzes: quizzesRouter,
  dashboard: dashboardRouter,
  users: usersRouter,
});

export type AppRouter = typeof appRouter;
export const createCaller = createCallerFactory(appRouter);
