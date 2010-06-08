/**
 * @file   logger.c
 *
 * @date   2009-05-22
 * @author Jean-Lou Dupont
 */
#include "cjld.h"
#include <stdarg.h>

#ifdef _DEBUG

char *_LOGGER_IDENTITY = "cjld";


/**
 * Crude logging function
 */
void doLog(int priority, const char *message, ...) {

	openlog(_LOGGER_IDENTITY, LOG_PID, LOG_LOCAL1);

	va_list ap;

	va_start(ap, message);
		vsyslog( priority, message, ap);
	va_end(ap);

	closelog();
}

#endif
