---
paths:
  - 'app/Models/**'
---

# Models

## Use UUID keys for domain models
Use UUID primary keys for domain models, with HasUuids on Eloquent models.

## Use the shared slug concern
Use HasSlug for name-derived model slugs; override slugUniqueScope() when uniqueness is scoped to a parent.
