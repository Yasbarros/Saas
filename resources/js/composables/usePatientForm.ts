import { useForm } from '@inertiajs/vue3';
import { z } from 'zod';

export interface PatientFormData {
  name: string;
  social_name: string;
  sex: string;
  cpf: string;
  cpf_legal_guardian: string;
  rg: string;
  date_of_birth: string;
  notes: string;
  phone: string;
  landline: string;
  email: string;
  postal_code: string;
  city: string;
  state: string;
  street: string;
  neighborhood: string;
  number: string;
  complement: string;
  photo: File | null;
  legal_guardian: string;
}

export function usePatientForm(initialData?: Partial<PatientFormData>) {
  const form = useForm<PatientFormData>({
    name: initialData?.name || '',
    social_name: initialData?.social_name || '',
    sex: initialData?.sex || '',
    cpf: initialData?.cpf || '',
    cpf_legal_guardian: initialData?.cpf_legal_guardian || '',
    rg: initialData?.rg || '',
    date_of_birth: initialData?.date_of_birth || '',
    notes: initialData?.notes || '',
    phone: initialData?.phone || '',
    landline: initialData?.landline || '',
    email: initialData?.email || '',
    postal_code: initialData?.postal_code || '',
    city: initialData?.city || '',
    state: initialData?.state || '',
    street: initialData?.street || '',
    neighborhood: initialData?.neighborhood || '',
    number: initialData?.number || '',
    complement: initialData?.complement || '',
    photo: null,
    legal_guardian: initialData?.legal_guardian || '',
  });

  const patientsSchema = z.object({
    name: z.string().min(1, 'O nome é obrigatório'),
    social_name: z.string().optional(),
    sex: z.enum(['M', 'F', 'O', 'N'], {
      errorMap: () => ({ message: 'O sexo é obrigatório' }),
    }),
    date_of_birth: z.string().min(1, 'A data de nascimento é obrigatória'),
    notes: z.string().optional(),
    cpf: z.string().optional(),
    cpf_legal_guardian: z.string().optional(),
    rg: z.string().optional(),
    phone: z.string().min(1, 'O celular é obrigatório'),
    landline: z.string().optional(),
    email: z.string().optional(),
    postal_code: z.string().min(1, 'O CEP é obrigatório'),
    city: z.string().min(1, 'A cidade é obrigatória'),
    state: z.string().min(1, 'O estado é obrigatório'),
    street: z.string().min(1, 'O logradouro é obrigatório'),
    neighborhood: z.string().min(1, 'O bairro é obrigatório'),
    number: z.string().optional(),
    complement: z.string().optional(),
    photo: z.instanceof(File).optional().nullable(),
    legal_guardian: z.string().optional(),
  }).refine((data) => {
    if (!data.date_of_birth) return true;
    
    const birthDate = new Date(data.date_of_birth);
    const today = new Date();
    let age = today.getFullYear() - birthDate.getFullYear();
    const m = today.getMonth() - birthDate.getMonth();
    
    if (m < 0 || (m === 0 && today.getDate() < birthDate.getDate())) {
      age--;
    }
    
    if (age < 18) {
      return !!data.cpf_legal_guardian && data.cpf_legal_guardian.length >= 11;
    } else {
      return !!data.cpf && data.cpf.length >= 11;
    }
  }, {
    message: 'CPF obrigatório e deve ter ao menos 11 caracteres',
    path: ['cpf', 'cpf_legal_guardian'],
  });

  const removeMasks = (data: PatientFormData): PatientFormData => {
    const cleanData = { ...data };
    
    const fieldsToClean = ['cpf', 'cpf_legal_guardian', 'phone', 'landline', 'postal_code'];
    
    fieldsToClean.forEach(field => {
      if (cleanData[field as keyof PatientFormData]) {
        cleanData[field as keyof PatientFormData] = (cleanData[field as keyof PatientFormData] as string)
          .replace(/\D/g, '');
      }
    });
    
    return cleanData;
  };

  const validateForm = () => {
    form.clearErrors();
    const validation = patientsSchema.safeParse(form.data());
    
    if (!validation.success) {
      const errors = validation.error.flatten().fieldErrors;
      for (const key in errors) {
        form.setError(key as keyof PatientFormData, errors[key as keyof typeof errors]?.join(', '));
      }
      return false;
    }
    
    return true;
  };

  const calculateAge = (dateOfBirth: string): number | string => {
    if (!dateOfBirth) return '';
    
    const today = new Date();
    const birthDate = new Date(dateOfBirth);
    let age = today.getFullYear() - birthDate.getFullYear();
    const month = today.getMonth() - birthDate.getMonth();
    
    if (month < 0 || (month === 0 && today.getDate() < birthDate.getDate())) {
      age--;
    }
    
    return age;
  };

  const isMinor = (age: number | string): boolean => {
    return typeof age === 'number' && age < 18;
  };

  const getCleanData = () => {
    return removeMasks(form.data());
  };

  return {
    form,
    patientsSchema,
    validateForm,
    calculateAge,
    isMinor,
    removeMasks,
    getCleanData,
  };
}