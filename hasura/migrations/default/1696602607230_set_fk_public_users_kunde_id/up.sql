alter table "public"."users"
  add constraint "users_kunde_id_fkey"
  foreign key ("kunde_id")
  references "public"."kunden"
  ("id") on update restrict on delete restrict;
