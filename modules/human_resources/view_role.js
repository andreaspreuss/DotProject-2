function submitRole(f)
{
	if(f.human_resources_role_name.value.length == 0) {
		alert('You must enter a role name.');
    		return false;
	}
  	f.submit();
  	return true;
}