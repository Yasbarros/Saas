import { InertiaForm } from "@inertiajs/vue3";

export function validateField(form: InertiaForm<any>, schema: any, field: string) {
  const result = schema.safeParse(form.data());

  if (!result.success) {
    const fieldError = result.error.flatten().fieldErrors[field]?.[0];
    form.setError(field, fieldError ?? '');
  } else {
    form.clearErrors(field);
  }
}

export function validateForm(schema: any, form: any): boolean {
  const result = schema.safeParse(form.data());

  if (!result.success) {
    const flattened = result.error.flatten().fieldErrors;

    form.clearErrors();

    Object.entries(flattened).forEach(([field, messages]) => {
      //TODO: corrigir warning do typescript
      if (messages?.[0]) {
        form.setError(field, messages[0]);
      }
    });

    return false;
  }

  return true;
}