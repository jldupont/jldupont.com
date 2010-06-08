/**
 * @file   macros.h
 *
 * @date   2009-05-22
 * @author Jean-Lou Dupont
 */

#ifndef MACROS_H_
#define MACROS_H_


#ifdef _DEBUG
#	define TESTPTR(REF, PTR) if (NULL==PTR) { doLog(LOG_ERR, #REF ": NULL pointer"); cjld_error_set(ECJLD_NULL_POINTER); return 0; }
#else
#	define TESTPTR(REF, PTR) if (NULL==PTR) { cjld_error_set(ECJLD_NULL_POINTER); return 0; }
#endif


#ifdef _DEBUG
#	define TESTPTRV(REF, PTR) if (NULL==PTR) { doLog(LOG_ERR, #REF ": NULL pointer"); cjld_error_set(ECJLD_NULL_POINTER); return NULL; }
#else
#	define TESTPTRV(REF, PTR) if (NULL==PTR) { cjld_error_set(ECJLD_NULL_POINTER); return NULL; }
#endif

#ifdef _DEBUG
#	define TESTINDEX(REF, IDX) if (IDX<0) { doLog(LOG_ERR, #REF ": invalid index [%i]", IDX); cjld_error_set(ECJLD_INVALID_INDEX); return 0; }
#else
#	define TESTINDEX(REF, IDX) if (IDX<0) { cjld_error_set(ECJLD_INVALID_INDEX); return 0; }


#ifdef _DEBUG
#	define TESTINDEXU(REF, IDX, MAX) if (IDX>=MAX) { doLog(LOG_ERR, #REF ": invalid index [%i]", IDX); cjld_error_set(ECJLD_INVALID_INDEX); return 0; }
#else
#	define TESTINDEXU(REF, IDX, MAX) if (IDX>=MAX) { cjld_error_set(ECJLD_INVALID_INDEX); return 0; }


#endif /* MACROS_H_ */
