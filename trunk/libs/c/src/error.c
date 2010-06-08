/**
 * @file   error.c
 *
 * @date   2009-05-22
 * @author Jean-Lou Dupont
 */
#include "cjld.h"


int __cjld_last_errno = ECJLD_NONE;


const char *__cjld_errors[] = {

		"***invalid code****",	//index 0
		"none",

		"memory allocation [malloc]",
		"NULL pointer",
		"invalid index",

};


//PRIVATE
void cjld_errno_set(int errno) {
	__cjld_last_errno = errno;
}

int cjld_errno(void) {

	return __cjld_last_errno;

}//

const char *cjld_errno_msg(int errno) {

	if ((sizeof(__cjld_errors) < errno) || (errno<0) ){
		return __cjld_errors[0];
	}

	return __cjld_errors[errno];

}//
