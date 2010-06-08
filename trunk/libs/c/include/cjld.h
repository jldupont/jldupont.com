/**
 * @file   cjld.h
 *
 * @date   2009-05-22
 * @author Jean-Lou Dupont
 *
 * @mainpage Welcome to the documentation for the library <b>cjld</b> -- $version
 *
 * node ID must be >0
 *
 */

#ifndef CJLD_H_
#define CJLD_H_

#include <errno.h>
#include <pthread.h>
#include <stdlib.h>
#include <sys/types.h>

#ifdef _DEBUG
#	include <syslog.h>
#	define DEBUG_LOG(...) doLog( __VA_ARGS__ )
#	define DEBUG_LOG_PTR(ptr, ...) if (NULL==ptr) doLog( __VA_ARGS__ )
	void doLog(int priority, char *message, ...);
#else
#	define DEBUG_LOG(...)
#	define DEBUG_LOG_PTR(...)
#endif


	/**
	 * Error codes
	 */
	enum {

		ECJLD_INVALID_CODE = 0,
		ECJLD_NONE = 1,

		ECJLD_MALLOC,
		ECJLD_NULL_POINTER,
		ECJLD_INVALID_INDEX,

	};

	/**
	 * Returns the last error encountered
	 */
	int cjld_errno(void);

	//private
	void cjld_errno_set(int errno);

	/**
	 * Returns a human readable error message
	 */
	const char *cjld_errno_msg(int errno);


	/**
	 * Single linked-list node
	 *
	 * @param node pointer to node
	 * @param next pointer to next node
	 */
	typedef struct _cjld_snode {

		int id;
		void *el;
		struct _cjld_snode *next;

	} cjld_snode;


	/**
	 * Double linked-list node
	 *
	 * @param node pointer to node
	 * @param next pointer to next node
	 */
	typedef struct _cjld_dnode {

		int id;
		void *el;
		struct _cjld_snode	*previous,
							*next;

	} cjld_dnode;



	typedef struct _cjld_queue_node {

		void *node;
		struct _cjld_queue_node *next;

	} cjld_queue_node;

	/**
	 * Queue - thread-safe
	 *
	 * @param mutex: mutex
	 * @param cond:  the condition variable
	 * @param head:  pointer to ``head``
	 * @param tail:  pointer to ``tail``
	 */
	typedef struct {
		pthread_cond_t  *cond;
		pthread_mutex_t *cond_mutex;
		pthread_mutex_t *mutex;

		struct _cjld_queue_node *head, *tail;
		int num;
		int id;
		int total_in;
		int total_out;
	} cjld_queue;


	/******************************************************
	 * QUEUE related
	 * Thread-safe
	 *****************************************************/

	cjld_queue *cjld_queue_create(int id);

	void  cjld_queue_destroy(cjld_queue *queue);

	int   cjld_queue_put_nb(cjld_queue *q, void *msg);
	int   cjld_queue_put(cjld_queue *q, void *msg);
	int	  cjld_queue_put_wait(cjld_queue *q, void *node);

	int   cjld_queue_put_head_nb(cjld_queue *q, void *node);
	int   cjld_queue_put_head(cjld_queue *q, void *msg);
	int   cjld_queue_put_head_wait(cjld_queue *q, void *node);


	void *cjld_queue_get(cjld_queue *q);
	void *cjld_queue_get_nb(cjld_queue *q);
	int   cjld_queue_wait(cjld_queue *q);

	int   cjld_queue_peek(cjld_queue *q);
	void  cjld_queue_signal(cjld_queue *q);


	/********************************************************
	 * LIST related
	 *
	 * Modeled after Python lists
	 *******************************************************/

	typedef struct _cjld_list {

		int count;
		int id;
		void (*cleaner)(void *el, int id);
		cjld_snode *head;
		cjld_snode *tail;

	} cjld_list;


	/**
	 * Creates a empty list
	 *
	 * The specified 'cleaner' function will be used
	 * when elements are removed from the list or
	 * when the list is destroyed.
	 *
	 * If the 'cleaner' function is set to NULL,
	 * the standard 'free' function is used.
	 *
	 * If the 'cleaner' parameter is set to -1,
	 * then no cleaning is performed.
	 *
	 * Id of 0 should be avoided.
	 *
	 * @param id unique identifier
	 * @param cleaner
	 */
	cjld_list *cjld_list_create(int id, void (*cleaner)(void *el, int id));


	/**
	 * Returns the id of the list
	 *
	 * @param list
	 */
	int cjld_list_getid(cjld_list *list);

	/**
	 * Returns the element count
	 */
	int cjld_list_count(cjld_list *list);

	/**
	 * Destroys a list along with its elements
	 * Applies the 'cleaner' function to each element (@see cjld_list_create)
	 *
	 *
	 * @param list
	 *
	 * @return 0 ERROR
	 * @return 1 SUCCESS
	 */
	int cjld_list_destroy(cjld_list *list);

	/**
	 * Appends an element to the end of the list
	 *
	 * Id of 0 should be avoided.
	 *
	 * @param el element
	 * @param id unique identifier for element [optional]
	 * @return 0 error
	 * @return 1 success
	 */
	int cjld_list_append( cjld_list *dest, void *el, int id );


	/**
	 * Extends an existing list to an existing list
	 *
	 * IMPORTANT NOTE: the inherited 'cleaner' comes from
	 *                 the list 'dest'
	 *
	 * @return dest
	 */
	cjld_list *cjld_list_extend( cjld_list *dest, cjld_list *src );


	/**
	 * Removes a specific element from a list
	 *
	 * @param list list
	 * @param id   unique identifier
	 * @return the element removed
	 */
	void *cjld_list_remove(cjld_list *list, int id);


	/**
	 * Removes & destroys a specific element from the list
	 */
	int cjld_list_remove_destroy(cjld_list *list, int id);


	/**
	 * Pops the last element from the list
	 *
	 * @return element
	 */
	void *cjld_list_pop(cjld_list *list);


	/**
	 * Inserts an element at a specific index in the list
	 *
	 * Use pos=0 for inserting at the head of the list
	 *
	 * @return 0 ERROR
	 * @return 1 SUCCESS
	 */
	int cjld_list_insert(cjld_list *list, void *el, int id, int pos);


	/**
	 * Slices a list
	 *
	 * Breaks a list in two lists. The extracted slice
	 * starts at 'start' and ends at 'stop' index positions.
	 *
	 * If index position 'stop' is out-of-bounds
	 *
	 * @return sliced list
	 */
	cjld_list *cjld_list_slice(cjld_list *list, int start, int stop);


	/**
	 * Applies a 'visitor' function to each element of the list
	 *
	 * @param list
	 * @param visitor
	 */
	int cjld_list_visit(cjld_list *list, void (*visitor)(void *el, int id));


#endif /* CJLD_H_ */
