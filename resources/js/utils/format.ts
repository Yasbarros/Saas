import { format } from 'date-fns';
import { ptBR } from 'date-fns/locale';

/**
 * Formata uma string ISO em `dd/MM/yyyy HH:mm`
 * @param dateStr string ISO (ex: "2025-06-01T16:33:23.000000Z")
 */
export function formatDateTime(dateStr: string): string {
  if (!dateStr) return '';
  const date = new Date(dateStr);
  return format(date, 'dd/MM/yyyy HH:mm', { locale: ptBR });
}
