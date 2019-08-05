<script>
import Router from 'svelte-spa-router';

import InvitesList from './Invites/InvitesList.svelte';
import Invite from './Invites/Invite.svelte';

const routes = {
    '/invites': InvitesList,
	'/invites/:inviteID': Invite,
}
</script>

<Router {routes}/>