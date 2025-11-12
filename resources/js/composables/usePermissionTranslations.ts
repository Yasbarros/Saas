export const usePermissionTranslations = () => {
    const permissionTranslations: Record<string, string> = {
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
    
    const moduleTranslations: Record<string, string> = {
      'users': 'Usuários',
      'roles': 'Funções', 
      'patients': 'Pacientes',
    };
    
    const getPermissionDisplayName = (permissionName: string): string => {
      return permissionTranslations[permissionName] || permissionName;
    };
    
    const getModuleDisplayName = (moduleKey: string): string => {
      return moduleTranslations[moduleKey] || moduleKey;
    };
    
    return { getPermissionDisplayName, getModuleDisplayName };
  };