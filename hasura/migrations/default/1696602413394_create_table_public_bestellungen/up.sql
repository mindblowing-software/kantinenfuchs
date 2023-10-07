CREATE TABLE "public"."bestellungen" ("id" serial NOT NULL, "datum" date NOT NULL, "kunde_id" integer NOT NULL, "user_id" integer NOT NULL, "anzahl" integer NOT NULL, "created_at" timestamptz NOT NULL DEFAULT now(), "updated_at" timestamptz NOT NULL DEFAULT now(), PRIMARY KEY ("id") , FOREIGN KEY ("user_id") REFERENCES "public"."users"("id") ON UPDATE restrict ON DELETE restrict, FOREIGN KEY ("kunde_id") REFERENCES "public"."kunden"("id") ON UPDATE restrict ON DELETE restrict, UNIQUE ("id"));COMMENT ON TABLE "public"."bestellungen" IS E'Bestellungen Table';
CREATE OR REPLACE FUNCTION "public"."set_current_timestamp_updated_at"()
RETURNS TRIGGER AS $$
DECLARE
  _new record;
BEGIN
  _new := NEW;
  _new."updated_at" = NOW();
  RETURN _new;
END;
$$ LANGUAGE plpgsql;
CREATE TRIGGER "set_public_bestellungen_updated_at"
BEFORE UPDATE ON "public"."bestellungen"
FOR EACH ROW
EXECUTE PROCEDURE "public"."set_current_timestamp_updated_at"();
COMMENT ON TRIGGER "set_public_bestellungen_updated_at" ON "public"."bestellungen" 
IS 'trigger to set value of column "updated_at" to current timestamp on row update';
