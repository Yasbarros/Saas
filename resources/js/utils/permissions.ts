export function can(permission: string, permissions: string[]): boolean {
    return permissions.includes(permission);
}

export const permissionLabels: Record<string, string> = {
    'view users': 'Visualizar usuários',
    'create users': 'Criar usuários',
    'edit users': 'Editar usuários',
    'delete users': 'Excluir usuários',
    'view roles': 'Visualizar funções',
    'create roles': 'Criar funções',
    'edit roles': 'Editar funções',
    'delete roles': 'Excluir funções',
    'view patients': 'Visualizar pacientes',
    'create patients': 'Criar pacientes',
    'edit patients': 'Editar pacientes',
    'delete patients': 'Excluir pacientes',
};

export function getPermissionLabel(permission: string): string {
    return permissionLabels[permission] || permission;
}