import actions from './actions'; 
import getters from './getters'; 
import mutations from './mutations'; 

const userModules = {
	// namespaced: true,
	state() {
		return {
			loggedIn: false,
			admin: false,
			usedId: 0,
			googleUser: null,
			adminEmail: 'jboullion83@gmail.com'
			//userEntity: {}
		};
	},
	actions: actions,
	getters: getters,
	mutations: mutations
}

export default userModules;