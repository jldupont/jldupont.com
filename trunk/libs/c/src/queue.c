/**
 * @file   queue.c
 *
 * @date   2009-05-22
 * @author Jean-Lou Dupont
 */
#include "cjld.h"


// PRIVATE
// =======
void *__cjld_queue_get_safe( cjld_queue *q);
int   __cjld_queue_put_head_safe( cjld_queue *q, void *node );
int   __cjld_queue_put_safe( cjld_queue *q, void *node );



/**
 * Creates a queue
 */
cjld_queue *cjld_queue_create(int id) {

	pthread_mutex_t *cond_mutex = malloc( sizeof(pthread_mutex_t) );
	if (NULL==cond_mutex) {
		return NULL;
	}

	pthread_cond_t *cond = malloc( sizeof (pthread_cond_t) );
	if (NULL == cond) {
		return NULL;
	}

	// if this malloc fails,
	//  there are much bigger problems that loom
	pthread_mutex_t *mutex = malloc( sizeof(pthread_mutex_t) );
	cjld_queue *q = malloc( sizeof(cjld_queue) );

	if ((NULL != q) && (NULL != mutex)){

		q->head  = NULL;
		q->tail  = NULL;
		q->num   = 0;
		q->id    = id;
		q->total_in  = 0;
		q->total_out = 0;

		pthread_mutex_init( mutex, NULL );
		pthread_mutex_init( cond_mutex, NULL );
		pthread_cond_init( cond, NULL );

		q->mutex      = mutex;
		q->cond_mutex = cond_mutex;
		q->cond       = cond;

	} else {

		DEBUG_LOG(LOG_DEBUG, "cjld_queue_create: MALLOC ERROR");

		if (NULL!=q)
			free(q);

		if (NULL!=mutex)
			pthread_mutex_destroy(mutex);

		if (NULL!=cond_mutex)
			pthread_mutex_destroy(cond_mutex);
	}

	return q;
}// init

/**
 * Destroys a queue
 *
 * This function is **not** aware of the
 *  message(s) potentially inside the queue,
 *  thus, the queue must be drained **before**
 *  using this function.
 *
 * The queue can be drained by:
 * - stopping the thread that ``puts``
 * - ``getting`` until NULL is returned
 *
 */
void cjld_queue_destroy(cjld_queue *q) {

	if (NULL==q) {

		DEBUG_LOG(LOG_DEBUG, "cjld_queue_destroy: NULL queue ptr");
		return;
	}
	pthread_mutex_t *mutex = q->mutex;
	pthread_mutex_t *cond_mutex = q->cond_mutex;
	pthread_cond_t  *cond  = q->cond;

	pthread_mutex_lock( mutex );
		free(q);
		q=NULL;
	pthread_mutex_unlock( mutex );

	pthread_mutex_destroy(mutex);
	pthread_mutex_destroy(cond_mutex);
	pthread_cond_destroy(cond);

}//



/**
 * Queues a node (blocking)
 *
 * @return 1 => success
 * @return 0 => error
 *
  */
int cjld_queue_put(cjld_queue *q, void *node) {

	if ((NULL==q) || (NULL==node)) {
		DEBUG_LOG(LOG_DEBUG, "cjld_queue_put: NULL queue/node ptr");
		return 0;
	}

	pthread_mutex_lock( q->mutex );

		int code = __cjld_queue_put_safe( q, node );

	pthread_mutex_unlock( q->mutex );
	if (code)
		pthread_cond_signal( q->cond );

	//DEBUG_LOG(LOG_DEBUG,"cjld_queue_put: q[%x] node[%x] END",q,node);

	return code;
}//[/queue_put]



/**
 * Queues a node (non-blocking)
 *
 * @return 1  => success
 * @return 0  => error
 * @return -1 => busy
 *
 */
int cjld_queue_put_nb(cjld_queue *q, void *node) {

	if ((NULL==q) || (NULL==node)) {
		DEBUG_LOG(LOG_DEBUG, "cjld_queue_put_nb: NULL queue/node ptr");
		return 0;
	}

	if (EBUSY == pthread_mutex_trylock( q->mutex ))
		return -1;

		int code = __cjld_queue_put_safe( q, node );

	pthread_mutex_unlock( q->mutex );
	if (code)
		pthread_cond_signal( q->cond );

	return code;
}//


/**
 * Queue Put Wait
 *
 * @return 0  ERROR
 * @return 1  SUCCESS
 *
 */
	int
cjld_queue_put_wait(cjld_queue *q, void *node) {

	if ((NULL==q) || (NULL==node)) {
		DEBUG_LOG(LOG_DEBUG, "cjld_queue_put_nb: NULL queue/node ptr");
		return 0;
	}

	int code;

	while(1) {

		// quick try... hopefully we get lucky
		if (EBUSY != pthread_mutex_trylock( q->mutex )) {
			code = __cjld_queue_put_safe( q, node );

			pthread_mutex_unlock( q->mutex );
			if (code)
				pthread_cond_signal( q->cond );

			break;

		} else {
			//DEBUG_LOG(LOG_DEBUG,"cjld_queue_put_wait: BEFORE LOCK q[%x][%i]", q, q->id);
			pthread_mutex_lock( q->cond_mutex );

				//DEBUG_LOG(LOG_DEBUG,"cjld_queue_put_wait: BEFORE COND_WAIT q[%x][%i]", q, q->id);
				int rc = pthread_cond_wait( q->cond, q->cond_mutex );
				if (rc) {
					DEBUG_LOG(LOG_ERR,"cjld_queue_put_wait: CONDITION WAIT ERROR");
				}

			pthread_mutex_unlock( q->cond_mutex );
			//DEBUG_LOG(LOG_DEBUG,"cjld_queue_put_wait: AFTER LOCK q[%x][%i]", q, q->id);
		}

	}

	return code;
}//

/**
 * Queue_put_safe
 *
 * Lock is not handled here - the caller must take
 * care of this.
 *
 * @return 0 => error
 * @return 1 => success
 *
 */
	int
__cjld_queue_put_safe( cjld_queue *q, void *node ) {

	int code = 1;
	cjld_queue_node *new_node=NULL;

	// if this malloc fails,
	//  there are much bigger problems that loom
	new_node = (cjld_queue_node *) malloc(sizeof(cjld_queue_node));
	if (NULL!=new_node) {

		// new node...
		new_node->node = node;
		new_node->next = NULL;

		// there is a tail... put at the end
		if (NULL!=q->tail)
			(q->tail)->next=new_node;

		// point tail to the new element
		q->tail = new_node;

		// adjust head
		if (NULL==q->head)
			q->head=new_node;

		q->total_in++;
		q->num++;
		//DEBUG_LOG(LOG_DEBUG,"__cjld_queue_put_safe: q[%x] id[%i] num[%i] in[%i] out[%i]", q, q->id, q->num, q->total_in, q->total_out);

	} else {

		code = 0;
	}

	return code;
}//


/**
 * Retrieves the next node from a queue
 *
 * @return NULL if none.
 *
 */
void *cjld_queue_get(cjld_queue *q) {

	if (NULL==q) {
		DEBUG_LOG(LOG_DEBUG, "cjld_queue_get: NULL queue ptr");
		return NULL;
	}

	pthread_mutex_lock( q->mutex );

		void *node=NULL;
		node = __cjld_queue_get_safe(q);

	pthread_mutex_unlock( q->mutex );

	return node;
}//[/queue_get]

/**
 * Retrieves the next node from a queue (non-blocking)
 *
 * @return NULL => No node OR BUSY
 *
 */
void *cjld_queue_get_nb(cjld_queue *q) {

	if (NULL==q) {
		DEBUG_LOG(LOG_DEBUG, "cjld_queue_get_nb: NULL queue ptr");
		return NULL;
	}

	if (EBUSY==pthread_mutex_trylock( q->mutex )) {
		return NULL;
	}

		void *node=NULL;
		node = __cjld_queue_get_safe(q);

	pthread_mutex_unlock( q->mutex );

	return node;
}//[/queue_get]


/**
 * Waits for a node in the queue
 *
 * @return 0 SUCCESS
 * @return 1 FAILURE
 *
 */
int cjld_queue_wait(cjld_queue *q) {

	if (NULL==q) {
		DEBUG_LOG(LOG_DEBUG, "cjld_queue_get_wait: NULL queue ptr");
		return 1;
	}

	//DEBUG_LOG(LOG_DEBUG,"cjld_queue_wait: BEFORE LOCK on q[%x][%i]",q,q->id);
	pthread_mutex_lock( q->cond_mutex );

		// it seems we need to wait...
		//DEBUG_LOG(LOG_DEBUG,"cjld_queue_wait: BEFORE COND_WAIT on q[%x][%i]",q,q->id);
		int rc = pthread_cond_wait( q->cond, q->cond_mutex );

		//int result2 = pthread_mutex_trylock( q->mutex );
		//DEBUG_LOG(LOG_DEBUG,"cjld_queue_get_wait: TRYLOCK q[%x][%i] result[%i] ",q,q->id,result2==EBUSY);

		if (rc) {
			DEBUG_LOG(LOG_ERR,"cjld_queue_get_wait: CONDITION WAIT ERROR");
		}

	pthread_mutex_unlock( q->cond_mutex );
	//DEBUG_LOG(LOG_DEBUG,"cjld_queue_wait: AFTER LOCK on q[%x][%i]",q,q->id);

	return rc;
}//

void *__cjld_queue_get_safe(cjld_queue *q) {

	cjld_queue_node *tmp = NULL;
	void *node=NULL;

	tmp = q->head;
	if (tmp!=NULL) {

		// the queue contained at least one node
		node = tmp->node;

		// adjust tail: case if tail==head
		//  ie. only one element present
		if (q->head == q->tail) {
			q->tail = NULL;
			q->head = NULL;
		} else {
			// adjust head : next pointer is already set
			//  to NULL in queue_put
			q->head = (q->head)->next;
		}

		//DEBUG_LOG(LOG_DEBUG,"cjld_queue_get: MESSAGE PRESENT, freeing queue_node[%x]", tmp);
		free(tmp);

		q->total_out++;
		q->num--;

		//{
		int count=0, in=q->total_in, out=q->total_out;
		tmp = q->head;
		while(tmp) {
			count++;
			tmp = tmp->next;
		}
		//DEBUG_LOG(LOG_DEBUG,"QQQ: q[%x] id[%3i] num[%3i] in[%4i] out[%4i] COUNT[%4i]", q, q->id, q->num, q->total_in, q->total_out, count);
		if ((in-out) != count) {
			DEBUG_LOG(LOG_ERR, "__cjld_queue_get_safe: >>> ERROR <<<  q[%x][%i]", q, q->id);
		}
		//}

	}

	return node;
}//

/**
 * Verifies if a message is present
 *
 * @return 1 if at least 1 message is present,
 * @return 0  if NONE
 * @return -1 on ERROR
 *
 */
int cjld_queue_peek(cjld_queue *q) {

	if (NULL==q) {
		DEBUG_LOG(LOG_DEBUG, "cjld_queue_peek: NULL queue ptr");
		return -1;
	}

	cjld_queue_node *tmp = NULL;
	int result = 0;

	pthread_mutex_lock( q->mutex );

		tmp = q->head;
		result = (tmp!=NULL);

	pthread_mutex_unlock( q->mutex );

	return result;
} // queue_peek





void cjld_queue_signal(cjld_queue *q) {
	int rc = pthread_cond_signal( q->cond );
	if (rc)
		DEBUG_LOG(LOG_DEBUG,"cjld_queue_signal: SIGNAL ERROR");
}




/**
 * Queues a node at the HEAD (non-blocking)
 *
 * @return 1  => success
 * @return 0  => error
 * @return -1 => busy
 *
 */
	int
cjld_queue_put_head_nb(cjld_queue *q, void *node) {

	if ((NULL==q) || (NULL==node)) {
		DEBUG_LOG(LOG_DEBUG, "cjld_queue_put_head_nb: NULL queue/node ptr");
		return 0;
	}

	if (EBUSY == pthread_mutex_trylock( q->mutex ))
		return -1;

		int code = __cjld_queue_put_head_safe( q, node );

	pthread_mutex_unlock( q->mutex );
	if (code)
		pthread_cond_signal( q->cond );

	return code;
}//[/queue_put]

/**
 * Puts a node at the HEAD of the queue
 *
 * This function is meant to support _high priority_ messages.
 *
 * @param q      queue reference
 * @param node   message reference
 *
 * @return 1 => success
 * @return 0 => error
 *
 */
int   cjld_queue_put_head(cjld_queue *q, void *node) {

	if ((NULL==q) || (NULL==node)) {
		DEBUG_LOG(LOG_DEBUG, "cjld_queue_put_head: NULL queue/node ptr");
		return 0;
	}

	pthread_mutex_lock( q->mutex );

		int code = __cjld_queue_put_head_safe( q, node );

	pthread_mutex_unlock( q->mutex );

	if (code)
		pthread_cond_signal( q->cond );

	//DEBUG_LOG(LOG_DEBUG,"queue_put: END");

	return code;

}//

/**
 * Queue Put Head Wait
 *
 * @return 0 ERROR
 * @return 1 SUCCESS
 *
 */
	int
cjld_queue_put_head_wait(cjld_queue *q, void *node) {

	if ((NULL==q) || (NULL==node)) {
		DEBUG_LOG(LOG_DEBUG, "cjld_queue_put_head_wait: NULL queue/node ptr");
		return 0;
	}

	int code;

	while(1) {

		// quick try... hopefully we get lucky
		if (EBUSY != pthread_mutex_trylock( q->mutex )) {

			code = __cjld_queue_put_head_safe( q, node );

			pthread_mutex_unlock( q->mutex );
			if (code)
				pthread_cond_signal( q->cond );

			break;

		} else {

			DEBUG_LOG(LOG_DEBUG,"cjld_queue_put_head_wait: BEFORE LOCK q[%x][%i]", q, q->id);
			pthread_mutex_lock( q->cond_mutex );

				DEBUG_LOG(LOG_DEBUG,"cjld_queue_put_head_wait: BEFORE COND_WAIT q[%x][%i]", q, q->id);
				int rc = pthread_cond_wait( q->cond, q->cond_mutex );
				if (rc) {
					DEBUG_LOG(LOG_ERR,"cjld_queue_put_wait: CONDITION WAIT ERROR");
				}

			pthread_mutex_unlock( q->cond_mutex );
			DEBUG_LOG(LOG_DEBUG,"cjld_queue_put_head_wait: AFTER LOCK q[%x][%i]", q, q->id);
		}

	}

	return code;

}//


/**
 * Puts a node a the HEAD of the queue
 * without regards to thread-safety
 *
 * @return 1  SUCCESS
 * @return 0  ERROR
 *
 */
	int
__cjld_queue_put_head_safe( cjld_queue *q, void *msg ) {

	int code = 1;
	cjld_queue_node *tmp=NULL;

	// if this malloc fails,
	//  there are much bigger problems that loom
	tmp = (cjld_queue_node *) malloc(sizeof(cjld_queue_node));
	if (NULL!=tmp) {

		tmp->node = msg;
		tmp->next = NULL;

		// there is a head... put at the front
		if (NULL!=q->head) {
			tmp->next = q->head;
		}

		// adjust head
		q->head = tmp;

		// adjust tail
		if (NULL==q->tail)
			q->tail=tmp;

		q->total_in++;
		q->num++;

	} else {

		code = 0;
	}

	return code;
}//
